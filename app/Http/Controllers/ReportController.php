<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        $stockIn = StockMovement::where('type', 'masuk')->whereBetween('date', [$from, $to]);
        $stockOut = StockMovement::where('type', 'keluar')->whereBetween('date', [$from, $to]);

        $summary = [
            'total_masuk' => (clone $stockIn)->sum('qty'),
            'nilai_masuk' => (clone $stockIn)->sum('total'),
            'total_keluar' => (clone $stockOut)->sum('qty'),
            'nilai_keluar' => (clone $stockOut)->sum('total'),
            'total_barang' => Item::count(),
            'nilai_stok' => Item::selectRaw('SUM(stock * purchase_price) as total')->value('total') ?? 0,
            'peminjaman_aktif' => Loan::where('status', 'dipinjam')->count(),
            'peminjaman_terlambat' => Loan::where('status', 'dipinjam')->where('due_date', '<', now())->count(),
            'peminjaman_menunggu' => Loan::where('approval_status', 'menunggu')->count(),
            'maintenance_proses' => Maintenance::where('status', 'diproses')->count(),
            'biaya_maintenance' => (clone (new Maintenance))->whereBetween('start_date', [$from, $to])->sum('cost'),
        ];

        $topOut = StockMovement::with('item')
            ->where('type', 'keluar')
            ->whereBetween('date', [$from, $to])
            ->selectRaw('item_id, SUM(qty) as total_qty')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        $lowStockItems = Item::with('category')->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get();

        $movements = StockMovement::with('item')
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('date')
            ->limit(50)
            ->get();

        $stockByWarehouse = Warehouse::withSum('itemStocks', 'qty')
            ->orderBy('name')
            ->get();

        return view('reports.index', compact(
            'summary', 'topOut', 'lowStockItems', 'movements', 'from', 'to', 'stockByWarehouse'
        ));
    }

    /**
     * CSV exports open directly in Excel/Google Sheets. Combined with the
     * existing browser print button (window.print → "Save as PDF"), the
     * report page can be exported as either Excel or PDF without adding
     * any extra PHP dependency.
     */
    public function exportCsv(Request $request, string $type): StreamedResponse
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        return match ($type) {
            'stok' => $this->csv('laporan-stok', ['Kode', 'Nama Barang', 'Kategori', 'Gudang', 'Stok', 'Min. Stok', 'Satuan', 'Harga Beli', 'Nilai Stok'],
                Item::with(['category', 'warehouse'])->orderBy('name')->get()->map(fn ($i) => [
                    $i->code, $i->name, $i->category->name ?? '-', $i->warehouse->name ?? '-',
                    $i->stock, $i->min_stock, $i->unit, $i->purchase_price, $i->stock * $i->purchase_price,
                ])),

            'transaksi' => $this->csv('laporan-transaksi-' . $from . '-sd-' . $to, ['Tanggal', 'Tipe', 'Referensi', 'Barang', 'Gudang', 'Qty', 'Harga Satuan', 'Total'],
                StockMovement::with(['item', 'warehouse'])->whereBetween('date', [$from, $to])->orderBy('date')->get()->map(fn ($m) => [
                    $m->date->format('Y-m-d'), ucfirst($m->type), $m->reference_no, $m->item->name ?? '-',
                    $m->warehouse->name ?? '-', $m->qty, $m->unit_price, $m->total,
                ])),

            'peminjaman' => $this->csv('laporan-peminjaman', ['Kode', 'Peminjam', 'Divisi', 'Tgl Pinjam', 'Jatuh Tempo', 'Status', 'Persetujuan'],
                Loan::orderByDesc('loan_date')->get()->map(fn ($l) => [
                    $l->code, $l->borrower_name, $l->borrower_division, $l->loan_date->format('Y-m-d'),
                    $l->due_date->format('Y-m-d'), $l->status, $l->approval_status,
                ])),

            default => abort(404),
        };
    }

    protected function csv(string $filename, array $header, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accented/IDR text correctly
            fputcsv($out, $header);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename . '.csv', ['Content-Type' => 'text/csv']);
    }
}
