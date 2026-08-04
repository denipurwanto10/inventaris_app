<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label — {{ $item->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; background: #f4f6f4; }
        .label { width: 320px; background: #fff; border: 1px solid #dde3df; border-radius: 10px; padding: 16px; text-align: center; margin: 0 auto; }
        .label h1 { font-size: 14px; margin: 0 0 2px; }
        .label p { font-size: 11px; color: #667; margin: 0 0 10px; }
        .print-btn { display: block; margin: 16px auto 0; padding: 8px 16px; border-radius: 8px; border: 1px solid #ccc; background: #fff; cursor: pointer; }
        @media print { .print-btn { display: none; } body { background: #fff; padding: 0; } }
    </style>
</head>
<body>
    <div class="label">
        <h1>{{ $item->name }}</h1>
        <p>{{ $item->code }} &middot; {{ $item->category->name ?? '' }}</p>
        <svg id="barcode"></svg>
    </div>
    <button class="print-btn" onclick="window.print()">Cetak Label</button>
    <script>
        JsBarcode('#barcode', @json($item->barcode), { width: 2, height: 55, fontSize: 12, margin: 6 });
    </script>
</body>
</html>
