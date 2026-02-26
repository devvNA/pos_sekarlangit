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

        .alert-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .alert-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            display: none;
        }

        .alert-warning strong {
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .alert-warning ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }

        .alert-warning li {
            margin: 4px 0;
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
            .alert-info,
            .alert-warning,
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

        <div class="alert-info">
            <strong>ℹ️ Tips Cetak Struk Thermal:</strong><br>
            Pastikan printer thermal Bluetooth sudah tersambung ke komputer/HP sebelum mencetak.
        </div>

        <div id="printer-warning" class="alert-warning">
            <strong>⚠️ Printer Tidak Tersedia</strong>
            <p>Printer tidak terdeteksi atau belum tersambung. Silakan:</p>
            <ul>
                <li>Nyalakan printer thermal Bluetooth Anda</li>
                <li>Pastikan printer sudah terpasang (paired) dengan perangkat ini</li>
                <li>Refresh halaman setelah printer terhubung</li>
                <li>Atau gunakan tombol "Simpan PDF" sebagai alternatif</li>
            </ul>
        </div>

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
            <button id="print-btn" class="btn btn-primary" type="button" onclick="handlePrint()">
                🖨️ Cetak Struk
            </button>
        </div>
    </div>

    <script>
        let printerAvailable = true;

        // Cek ketersediaan printer
        async function checkPrinter() {
            try {
                // Cek apakah browser support print API
                if (!window.print) {
                    showPrinterWarning();
                    return false;
                }

                // Untuk browser modern, cek printer devices jika API tersedia
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        // Note: Browser tidak bisa langsung deteksi printer, hanya media devices
                        // Jadi kita asumsikan printer tersedia jika browser support print
                        printerAvailable = true;
                    } catch (err) {
                        // Jika gagal enumerate devices, tetap lanjut ke print
                        printerAvailable = true;
                    }
                }

                return true;
            } catch (error) {
                showPrinterWarning();
                return false;
            }
        }

        function showPrinterWarning() {
            const warning = document.getElementById('printer-warning');
            if (warning) {
                warning.style.display = 'block';
            }
            printerAvailable = false;
        }

        function hidePrinterWarning() {
            const warning = document.getElementById('printer-warning');
            if (warning) {
                warning.style.display = 'none';
            }
        }

        function handlePrint() {
            const printBtn = document.getElementById('print-btn');
            const originalText = printBtn.innerHTML;

            // Ubah button text
            printBtn.innerHTML = '🖨️ Membuka printer...';
            printBtn.disabled = true;

            try {
                // Trigger print dialog
                window.print();

                // Reset button setelah beberapa saat
                setTimeout(() => {
                    printBtn.innerHTML = originalText;
                    printBtn.disabled = false;
                }, 1000);
            } catch (error) {
                console.error('Print error:', error);
                alert(
                    '❌ Gagal membuka dialog print.\n\nPastikan:\n• Printer tersambung\n• Browser mengizinkan print\n• Tidak ada popup blocker'
                );

                printBtn.innerHTML = originalText;
                printBtn.disabled = false;
                showPrinterWarning();
            }
        }

        // Event handler untuk afterprint
        window.addEventListener('afterprint', function() {
            const printBtn = document.getElementById('print-btn');
            if (printBtn) {
                printBtn.innerHTML = '🖨️ Cetak Struk';
                printBtn.disabled = false;
            }
        });

        // Event handler untuk beforeprint
        window.addEventListener('beforeprint', function() {
            hidePrinterWarning();
        });

        // Keyboard shortcut: Ctrl+P atau Cmd+P
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                handlePrint();
            }
        });

        // Auto focus untuk kemudahan keyboard navigation
        document.addEventListener('DOMContentLoaded', function() {
            checkPrinter();

            // Optional: Auto print setelah load (uncomment jika diinginkan)
            // setTimeout(() => handlePrint(), 800);
        });
    </script>
</body>

</html>
