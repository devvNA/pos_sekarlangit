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
            padding: 20px;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 400px;
            width: 100%;
        }

        .success-alert {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .success-alert strong {
            font-weight: 600;
        }

        .receipt-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 24px;
            margin-bottom: 16px;
        }

        .receipt {
            width: 100%;
            max-width: 72mm;
            margin: 0 auto;
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

        .action-buttons {
            display: flex;
            gap: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                min-height: auto;
                display: block;
                align-items: initial;
            }

            .container {
                max-width: none;
            }

            .success-alert,
            .action-buttons {
                display: none !important;
            }

            .receipt-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .receipt {
                display: block !important;
                max-width: none !important;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .receipt-wrapper {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @if (session('success'))
            <div class="success-alert">
                <strong>✓ {{ session('success') }}</strong>
            </div>
        @endif

        <div class="receipt-wrapper">
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
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('pos.index') }}" class="btn btn-secondary">
                ← Kembali ke Kasir
            </a>
            <button class="btn btn-primary" type="button" onclick="window.print()">
                Cetak Struk
            </button>
        </div>
    </div>

    <script>
        // Auto focus untuk kemudahan keyboard navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Auto print setelah load (uncomment jika diinginkan)
            // setTimeout(() => window.print(), 500);
        });
    </script>
</body>

</html>
