<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Struk {{ $sale->receipt_no }}</title>
        <style>
            @page {
                size: 80mm auto;
                margin: 6mm;
            }
            body {
                font-family: "Courier New", Courier, monospace;
                color: #111;
                margin: 0;
            }
            .receipt {
                width: 72mm;
            }
            .center {
                text-align: center;
            }
            .row {
                display: flex;
                justify-content: space-between;
                gap: 8px;
            }
            .muted {
                color: #555;
            }
            .line {
                border-top: 1px dashed #333;
                margin: 8px 0;
            }
            .btn-print {
                margin: 12px 0;
                padding: 8px 12px;
                border: 1px solid #111;
                background: #fff;
                font-size: 12px;
                cursor: pointer;
            }
            @media print {
                .btn-print {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="receipt">
            <div class="center">
                <strong>Toko Sekarlangit</strong>
                <div class="muted">Karanggintung</div>
            </div>
            <div class="line"></div>
            <div>
                <div>No: {{ $sale->receipt_no }}</div>
                <div>{{ $sale->sold_at->format('d/m/Y H:i') }}</div>
                <div class="muted">Metode: {{ strtoupper($sale->payment_method) }}</div>
            </div>
            <div class="line"></div>
            <div>
                @foreach ($sale->items as $item)
                    <div>{{ $item->product->name ?? 'Produk' }}</div>
                    <div class="row">
                        <span>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="line"></div>
            <div class="row">
                <strong>Total</strong>
                <strong>{{ number_format($sale->total, 0, ',', '.') }}</strong>
            </div>
            <div class="row">
                <span>Bayar</span>
                <span>{{ number_format($sale->paid, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>Kembali</span>
                <span>{{ number_format($sale->change, 0, ',', '.') }}</span>
            </div>
            <div class="line"></div>
            <div class="center muted">Terima kasih</div>
            <button class="btn-print" type="button" onclick="window.print()">Cetak Struk</button>
        </div>
    </body>
</html>
