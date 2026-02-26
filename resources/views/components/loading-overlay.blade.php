<!-- Loading Overlay -->
<div id="loading-overlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300"
    style="display: none;">

    <div class="flex flex-col items-center gap-4">
        <!-- Spinner -->
        <div class="relative h-16 w-16">
            <!-- Outer rotating ring -->
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-white border-r-white opacity-100"
                style="animation: spin 1s linear infinite;"></div>
            <!-- Inner rotating ring opposite direction -->
            <div class="absolute inset-2 rounded-full border-4 border-transparent border-b-white border-l-white opacity-75"
                style="animation: spin 1.5s linear reverse infinite;"></div>
        </div>

        <!-- Loading Text -->
        <div class="text-center">
            <p class="text-lg font-semibold text-white">Memuat halaman...</p>
            <p class="mt-1 text-sm text-white/70">Silakan tunggu</p>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    #loading-overlay {
        transition: opacity 0.3s ease-in-out;
    }

    #loading-overlay.show {
        display: flex !important;
        opacity: 1;
    }

    #loading-overlay:not(.show) {
        opacity: 0;
    }

    /* Mirror camera feed for barcode scanners */
    #scanner-wrapper video,
    #inventory-scanner-wrapper video {
        transform: scaleX(-1);
    }
</style>
