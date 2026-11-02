<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::count();
        $totalStock = Item::sum('stock');
        $totalStockValue = Item::selectRaw('SUM(stock * purchase_price) as total')->value('total') ?? 0;
        $lowStock = Item::whereColumn('stock', '<=', 'min_stock')->count();
        $activeLoans = Loan::where('status', 'dipinjam')->count();
        $pendingLoans = Loan::where('approval_status', 'menunggu')->count();
        $inProgressMaintenance = Maintenance::where('status', 'diproses')->count();

        $outThisMonth = StockMovement::where('type', 'keluar')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('qty');

        $inThisMonth = StockMovement::where('type', 'masuk')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('qty');

        // Window is 6 months. When nothing moved recently (demo/stale data), anchor the
        // window on the latest month that still has movements so the chart isn't blank.
        $latestMovement = StockMovement::max('date');
        $latestMonth = $latestMovement ? \Illuminate\Support\Carbon::parse($latestMovement)->startOfMonth() : null;
        $thisMonth = now()->startOfMonth();
        $anchor = ($latestMonth && $latestMonth->lt($thisMonth->copy()->subMonths(5))) ? $latestMonth : $thisMonth;
        $chartStart = $anchor->copy()->subMonths(5);

        $chartData = StockMovement::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as bulan"),
                'type',
                DB::raw('SUM(qty) as total')
            )
            ->where('date', '>=', $chartStart)
            ->groupBy('bulan', 'type')
            ->orderBy('bulan')
            ->get();

        // Every month in the window gets a slot, including months with no movement.
        $chartMonths = collect(range(0, 5))->map(fn ($i) => $chartStart->copy()->addMonths($i)->format('Y-m'))->all();
        $chartRange = $chartStart->locale('id')->isoFormat('MMM') . ' – ' . $anchor->copy()->locale('id')->isoFormat('MMM YYYY');

        $lowStockItems = Item::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(6)
            ->get();

        $recentMovements = StockMovement::with('item')
            ->latest('date')
            ->latest('id')
            ->limit(8)
            ->get();

        // Category composition of total stock value — powers a doughnut chart.
        $categoryValue = Item::select('category_id', DB::raw('SUM(stock * purchase_price) as total'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        // Top 5 most-moved-out items in the last 90 days.
        $topMovingItems = StockMovement::with('item')
            ->where('type', 'keluar')
            ->where('date', '>=', now()->subDays(90))
            ->selectRaw('item_id, SUM(qty) as total_qty')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $warehouseStock = Warehouse::withSum('itemStocks', 'qty')->orderBy('name')->get();

        return view('dashboard.index', compact(
            'totalItems', 'totalStock', 'totalStockValue', 'lowStock', 'activeLoans', 'pendingLoans',
            'inProgressMaintenance', 'outThisMonth', 'inThisMonth',
            'chartData', 'chartMonths', 'chartRange', 'lowStockItems', 'recentMovements',
            'categoryValue', 'topMovingItems', 'warehouseStock'
        ));
    }
}
