import { Html5Qrcode } from 'html5-qrcode';
import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('app-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const totalEl = document.getElementById('pos-total');
    const paidInput = document.getElementById('paid-input');
    const changeInput = document.getElementById('change-input');
    const paymentSelect = document.querySelector('select[name="payment_method"]');

    // Scanner elements (modal)
    const scanToggle = document.getElementById('scan-toggle');
    const scannerPreview = document.getElementById('scanner-preview');
    const scannerStatus = document.getElementById('scanner-status');
    const scannerWrapper = document.getElementById('scanner-wrapper');
    const scanLine = document.getElementById('scan-line');
    const modalBarcodeInput = document.getElementById('modal-barcode-input');
    const modalAddForm = document.getElementById('modal-add-form');

    // Inventory scanner elements
    const inventoryBarcodeInput = document.getElementById('inventory-barcode-input');
    const inventoryScanToggle = document.getElementById('inventory-scan-toggle');
    const inventoryManualInput = document.getElementById('inventory-manual-input');
    const inventoryScannerPreview = document.getElementById('inventory-scanner-preview');
    const inventoryScannerStatus = document.getElementById('inventory-scanner-status');

    const rupiahInputs = document.querySelectorAll('[data-rupiah]');
    // Hapus semua karakter non-digit
    const stripNonDigits = (value) => (value ?? '').toString().replace(/\D+/g, '');
    // Format angka dengan thousand separator (titik untuk ID)
    const formatThousandSeparator = (value) => {
        const digits = stripNonDigits(value);
        if (!digits) return '';
        return Number(digits).toLocaleString('id-ID');
    };
    // Normalisasi input: hanya thousand separator, tanpa prefix Rp
    const normalizeRupiahInput = (input) => {
        const originalValue = input.value;
        // Hapus .00 di akhir jika ada (dari database decimal)
        const cleanValue = originalValue.toString().replace(/\.00$/, '');
        const newValue = formatThousandSeparator(cleanValue);
        if (originalValue !== newValue) {
            input.value = newValue;
        }
    };

    let scanner = null;
    let scanning = false;
    let inventoryScanner = null;
    let inventoryScanning = false;
    let lastScanAt = 0;
    let lastInventoryScanAt = 0;
    const scanBeep = new Audio('/audio/scanner-beep.mp3');

    // Export scanner state untuk akses dari view
    window.posScanner = {
        get instance() { return scanner; },
        get isScanning() { return scanning; },
        set isScanning(value) { scanning = value; }
    };
    window.inventoryScanner = {
        get instance() { return inventoryScanner; },
        get isScanning() { return inventoryScanning; },
        set isScanning(value) { inventoryScanning = value; }
    };

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

    const updateChange = () => {
        if (!totalEl || !paidInput || !changeInput) {
            return;
        }
        const total = Number(totalEl.dataset.total || 0);
        const paid = Number(stripNonDigits(paidInput.value || 0));
        const change = paid > total ? paid - total : 0;
        changeInput.value = `Rp ${change.toLocaleString('id-ID')}`;
    };

    if (paidInput) {
        paidInput.addEventListener('input', updateChange);
    }

    if (paymentSelect && paidInput) {
        paymentSelect.addEventListener('change', (event) => {
            if (event.target.value === 'kasbon') {
                paidInput.value = '0';
                paidInput.setAttribute('disabled', 'disabled');
                updateChange();
            } else {
                paidInput.removeAttribute('disabled');
            }
        });
    }

    updateChange();

    if (rupiahInputs.length > 0) {
        rupiahInputs.forEach((input) => {
            normalizeRupiahInput(input);
            input.addEventListener('input', () => normalizeRupiahInput(input));
        });

        const forms = new Set();
        rupiahInputs.forEach((input) => {
            const form = input.closest('form');
            if (form) {
                forms.add(form);
            }
        });

        forms.forEach((form) => {
            form.addEventListener('submit', () => {
                form.querySelectorAll('[data-rupiah]').forEach((input) => {
                    // Kirim hanya angka tanpa separator
                    input.value = stripNonDigits(input.value);
                });
            });
        });
    }

    // Modal barcode input handling
    if (modalBarcodeInput) {
        modalBarcodeInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (modalAddForm) {
                    modalAddForm.requestSubmit();
                }
            }
        });
    }

    // POS Scanner (in modal)
    if (scanToggle && scannerPreview) {
        scanToggle.addEventListener('click', async () => {
            if (!scanner) {
                scanner = new Html5Qrcode(scannerPreview.id);
            }

            if (!scanning) {
                try {
                    scanning = true;
                    if (window.posScanner) window.posScanner.isScanning = true;
                    scanToggle.textContent = 'Hentikan Scan';
                    if (scannerWrapper) scannerWrapper.classList.add('scanning-active');
                    if (scanLine) scanLine.classList.add('scanning');
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: memulai kamera...';
                    }
                    await scanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 260, height: 160 },
                        },
                        (decodedText) => {
                            const now = Date.now();
                            if (now - lastScanAt < 1500) {
                                return;
                            }
                            lastScanAt = now;
                            if (modalBarcodeInput) {
                                modalBarcodeInput.value = decodedText;
                            }
                            if (scannerStatus) {
                                scannerStatus.textContent = `Status: terbaca ${decodedText}`;
                            }
                            playBeep();
                            if (modalAddForm) {
                                modalAddForm.requestSubmit();
                            }
                        }
                    );
                } catch (error) {
                    scanning = false;
                    if (window.posScanner) window.posScanner.isScanning = false;
                    scanToggle.textContent = 'Mulai Scan Kamera';
                    if (scannerWrapper) scannerWrapper.classList.remove('scanning-active');
                    if (scanLine) scanLine.classList.remove('scanning');
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: gagal membuka kamera.';
                    }
                }
            } else {
                try {
                    await scanner.stop();
                } finally {
                    scanning = false;
                    if (window.posScanner) window.posScanner.isScanning = false;
                    scanToggle.textContent = 'Mulai Scan Kamera';
                    if (scannerWrapper) scannerWrapper.classList.remove('scanning-active');
                    if (scanLine) scanLine.classList.remove('scanning');
                    if (scannerStatus) {
                        scannerStatus.textContent = 'Status: berhenti.';
                    }
                }
            }
        });
    }

    // Inventory Scanner (in modal)
    const inventoryScanToggleModal = document.getElementById('inventory-scan-toggle-modal');
    const inventoryScannerWrapper = document.getElementById('inventory-scanner-wrapper');
    const inventoryScanLine = document.getElementById('inventory-scan-line');
    const modalInventoryBarcodeInput = document.getElementById('modal-inventory-barcode-input');
    const mainInventoryBarcodeInput = document.getElementById('inventory-barcode-input');

    if (inventoryScanToggleModal && inventoryScannerPreview) {
        inventoryScanToggleModal.addEventListener('click', async () => {
            if (!inventoryScanner) {
                inventoryScanner = new Html5Qrcode(inventoryScannerPreview.id);
            }

            if (!inventoryScanning) {
                try {
                    inventoryScanning = true;
                    if (window.inventoryScanner) window.inventoryScanner.isScanning = true;
                    inventoryScanToggleModal.textContent = 'Hentikan Scan';
                    if (inventoryScannerWrapper) inventoryScannerWrapper.classList.add('scanning-active');
                    if (inventoryScanLine) inventoryScanLine.classList.add('scanning');
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: memulai kamera...';
                    }
                    await inventoryScanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 260, height: 160 },
                        },
                        (decodedText) => {
                            const now = Date.now();
                            if (now - lastInventoryScanAt < 1500) {
                                return;
                            }
                            lastInventoryScanAt = now;
                            if (modalInventoryBarcodeInput) {
                                modalInventoryBarcodeInput.value = decodedText;
                            }
                            if (mainInventoryBarcodeInput) {
                                mainInventoryBarcodeInput.value = decodedText;
                            }
                            if (inventoryScannerStatus) {
                                inventoryScannerStatus.textContent = `Status: terbaca ${decodedText}`;
                            }
                            playBeep();
                            // Auto close modal after successful scan
                            setTimeout(() => {
                                const closeBtn = document.getElementById('close-inventory-scan-modal');
                                if (closeBtn) closeBtn.click();
                            }, 500);
                        }
                    );
                } catch (error) {
                    inventoryScanning = false;
                    if (window.inventoryScanner) window.inventoryScanner.isScanning = false;
                    inventoryScanToggleModal.textContent = 'Mulai Scan Kamera';
                    if (inventoryScannerWrapper) inventoryScannerWrapper.classList.remove('scanning-active');
                    if (inventoryScanLine) inventoryScanLine.classList.remove('scanning');
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: gagal membuka kamera.';
                    }
                }
            } else {
                try {
                    await inventoryScanner.stop();
                } finally {
                    inventoryScanning = false;
                    if (window.inventoryScanner) window.inventoryScanner.isScanning = false;
                    inventoryScanToggleModal.textContent = 'Mulai Scan Kamera';
                    if (inventoryScannerWrapper) inventoryScannerWrapper.classList.remove('scanning-active');
                    if (inventoryScanLine) inventoryScanLine.classList.remove('scanning');
                    if (inventoryScannerStatus) {
                        inventoryScannerStatus.textContent = 'Status: berhenti.';
                    }
                }
            }
        });
    }
});

// ── Global form submission loading indicator ──
(function () {
    const SPINNER_SVG =
        '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
        '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25"></circle>' +
        '<path fill="currentColor" style="opacity:0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
        '</svg>';

    const DEFAULT_LOADING_TEXT = 'Memproses...';

    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (form.hasAttribute('data-no-loading')) return;

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        let button = event.submitter;
        if (!button) {
            button = form.querySelector('button[type="submit"], input[type="submit"]');
        }

        if (!button || button.disabled) return;
        if (button.hasAttribute('data-no-loading')) return;

        form.dataset.submitting = 'true';

        const loadingText = button.getAttribute('data-loading-text') || DEFAULT_LOADING_TEXT;

        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' + SPINNER_SVG + '<span>' + loadingText + '</span></span>';
        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            document.querySelectorAll('form[data-submitting="true"]').forEach(function (form) {
                delete form.dataset.submitting;
            });
            document.querySelectorAll('button[data-original-html]').forEach(function (button) {
                button.innerHTML = button.dataset.originalHtml;
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-not-allowed');
                delete button.dataset.originalHtml;
            });
        }
    });
})();
// ── Page Navigation Loading Indicator ──
(function () {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (!loadingOverlay) return;

    let loadingTimeout = null;

    const showLoading = () => {
        clearTimeout(loadingTimeout);
        loadingOverlay.classList.add('show');
    };

    const hideLoading = () => {
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
            loadingOverlay.classList.remove('show');
        }, 300);
    };

    // Show loading when page is being unloaded
    window.addEventListener('beforeunload', () => {
        showLoading();
    });

    // Hide loading when page is fully loaded
    if (document.readyState === 'complete') {
        hideLoading();
    } else {
        window.addEventListener('load', hideLoading);
    }

    // Handle link clicks
    document.addEventListener('click', (event) => {
        // Traverse up to find anchor tag
        const link = event.target.closest('a');
        if (!link) return;

        // Skip if link has data-no-loading attribute
        if (link.hasAttribute('data-no-loading')) return;

        // Skip external links
        const href = link.getAttribute('href');
        if (!href || href.startsWith('http') || href.startsWith('//')) return;

        // Skip if target is blank
        if (link.target === '_blank') return;

        // Skip if prevented default
        if (event.defaultPrevented) return;

        // Show loading for navigation
        showLoading();
    }, true);

    // Handle form submissions (especially for navigation like logout)
    document.addEventListener('submit', (event) => {
        const form = event.target;

        // Skip if form has data-no-loading attribute
        if (form.hasAttribute('data-no-loading')) return;

        // Skip if form has data-no-navigate attribute
        if (form.hasAttribute('data-no-navigate')) return;

        // Show loading for form submission
        showLoading();
    }, true);

    // Also hide loading after a timeout (in case page load fails)
    window.addEventListener('pageshow', hideLoading);
})();