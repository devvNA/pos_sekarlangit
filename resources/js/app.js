import { Html5Qrcode } from 'html5-qrcode';
import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('app-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const barcodeInput = document.getElementById('barcode-input');
    const addForm = document.getElementById('pos-add-form');
    const totalEl = document.getElementById('pos-total');
    const paidInput = document.getElementById('paid-input');
    const changeInput = document.getElementById('change-input');
    const paymentSelect = document.querySelector('select[name="payment_method"]');
    const scanToggle = document.getElementById('scan-toggle');
    const manualInput = document.getElementById('manual-input');
    const scannerPreview = document.getElementById('scanner-preview');
    const scannerStatus = document.getElementById('scanner-status');
    const inventoryBarcodeInput = document.getElementById('inventory-barcode-input');
    const inventoryScanToggle = document.getElementById('inventory-scan-toggle');
    const inventoryManualInput = document.getElementById('inventory-manual-input');
    const inventoryScannerPreview = document.getElementById('inventory-scanner-preview');
    const inventoryScannerStatus = document.getElementById('inventory-scanner-status');
    let scanner = null;
    let scanning = false;
    let inventoryScanner = null;
    let inventoryScanning = false;
    let lastScanAt = 0;
    let lastInventoryScanAt = 0;
    const scanBeep = new Audio('/audio/scanner-beep.mp3');

    const playBeep = () => {
        try {
            scanBeep.currentTime = 0;
            const playResult = scanBeep.play();
            if (playResult && typeof playResult.catch === 'function') {
                playResult.catch(() => { });
            }
        } catch (error) {
            // Ignore audio playback errors (autoplay policy, missing device, etc.).
        }
    };

    const openSidebar = () => {
        if (!sidebar || !sidebarOverlay) {
            return;
        }
        sidebar.classList.remove('hidden');
        sidebarOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeSidebar = () => {
        if (!sidebar || !sidebarOverlay) {
            return;
        }
        sidebar.classList.add('hidden');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', openSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    if (barcodeInput) {
        barcodeInput.focus();
        barcodeInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (addForm) {
                    addForm.submit();
                }
            }
        });
    }

    const updateChange = () => {
        if (!totalEl || !paidInput || !changeInput) {
            return;
        }
        const total = Number(totalEl.dataset.total || 0);
        const paid = Number(paidInput.value || 0);
        const change = paid > total ? paid - total : 0;
        changeInput.value = `Rp ${change.toLocaleString('id-ID')}`;
    };

    if (paidInput) {
        paidInput.addEventListener('input', updateChange);
    }

    if (paymentSelect && paidInput) {
        paymentSelect.addEventListener('change', (event) => {
            if (event.target.value === 'kasbon') {
                paidInput.value = 0;
                paidInput.setAttribute('disabled', 'disabled');
                updateChange();
            } else {
                paidInput.removeAttribute('disabled');
            }
        });
    }

    updateChange();

    if (manualInput && barcodeInput) {
        manualInput.addEventListener('click', () => {
            barcodeInput.focus();
        });
    }

    if (scanToggle && scannerPreview) {
        scanToggle.addEventListener('click', async () => {
            if (!scanner) {
                scanner = new Html5Qrcode(scannerPreview.id);
            }

            if (!scanning) {
                try {
                    scanning = true;
                    scanToggle.textContent = 'Hentikan Scan';
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: memulai kamera...';
                    }
                    await scanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 240, height: 140 },
                        },
                        (decodedText) => {
                            const now = Date.now();
                            if (now - lastScanAt < 1500) {
                                return;
                            }
                            lastScanAt = now;
                            if (barcodeInput) {
                                barcodeInput.value = decodedText;
                                barcodeInput.focus();
                            }
                            if (scannerStatus) {
                                scannerStatus.textContent = `Status: terbaca ${decodedText}`;
                            }
                            playBeep();
                            if (addForm) {
                                addForm.submit();
                            }
                        }
                    );
                } catch (error) {
                    scanning = false;
                    scanToggle.textContent = 'Mulai Scan Kamera';
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: gagal membuka kamera.';
                    }
                }
            } else {
                try {
                    await scanner.stop();
                } finally {
                    scanning = false;
                    scanToggle.textContent = 'Mulai Scan Kamera';
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: berhenti.';
                    }
                }
            }
        });
    }

    if (inventoryManualInput && inventoryBarcodeInput) {
        inventoryManualInput.addEventListener('click', () => {
            inventoryBarcodeInput.focus();
        });
    }

    if (inventoryScanToggle && inventoryScannerPreview) {
        inventoryScanToggle.addEventListener('click', async () => {
            if (!inventoryScanner) {
                inventoryScanner = new Html5Qrcode(inventoryScannerPreview.id);
            }

            if (!inventoryScanning) {
                try {
                    inventoryScanning = true;
                    inventoryScanToggle.textContent = 'Hentikan Scan';
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: memulai kamera...';
                    }
                    await inventoryScanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 240, height: 140 },
                        },
                        (decodedText) => {
                            const now = Date.now();
                            if (now - lastInventoryScanAt < 1500) {
                                return;
                            }
                            lastInventoryScanAt = now;
                            if (inventoryBarcodeInput) {
                                inventoryBarcodeInput.value = decodedText;
                                inventoryBarcodeInput.focus();
                            }
                            if (inventoryScannerStatus) {
                                inventoryScannerStatus.textContent = `Status: terbaca ${decodedText}`;
                            }
                            playBeep();
                        }
                    );
                } catch (error) {
                    inventoryScanning = false;
                    inventoryScanToggle.textContent = 'Mulai Scan Kamera';
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: gagal membuka kamera.';
                    }
                }
            } else {
                try {
                    await inventoryScanner.stop();
                } finally {
                    inventoryScanning = false;
                    inventoryScanToggle.textContent = 'Mulai Scan Kamera';
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: berhenti.';
                    }
                }
            }
        });
    }
});
