@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmType' => 'danger',
    'icon' => 'warning',
])

@php
// Convert kebab-case ID to camelCase for function names
$functionName = str_replace(['-', '_'], ' ', $id);
$functionName = ucwords($functionName);
$functionName = str_replace(' ', '', $functionName);
$functionName = lcfirst($functionName);

$iconColors = [
    'warning' => 'text-amber-500 bg-amber-100',
    'danger' => 'text-red-500 bg-red-100',
    'info' => 'text-blue-500 bg-blue-100',
    'question' => 'text-gray-500 bg-gray-100',
    'success' => 'text-green-500 bg-green-100',
];

$buttonColors = [
    'danger' => 'bg-red-600 hover:bg-red-700',
    'warning' => 'bg-amber-600 hover:bg-amber-700',
    'primary' => 'bg-blue-600 hover:bg-blue-700',
    'success' => 'bg-green-600 hover:bg-green-700',
];

$iconSvgs = [
    'warning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
    'danger' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
    'info' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'question' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'success' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
];

$iconColor = $iconColors[$icon] ?? $iconColors['warning'];
$buttonColor = $buttonColors[$confirmType] ?? $buttonColors['danger'];
$iconSvg = $iconSvgs[$icon] ?? $iconSvgs['warning'];
@endphp

{{-- Modal Container --}}
<div id="{{ $id }}" style="display: none; position: fixed; inset: 0; z-index: 9999;" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div id="{{ $id }}-backdrop" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s;"></div>
    
    {{-- Modal Content --}}
    <div style="position: relative; height: 100%; display: flex; align-items: center; justify-content: center; padding: 1rem;">
        <div id="{{ $id }}-panel" style="background: white; border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); transform: scale(0.95); opacity: 0; transition: all 0.2s;">
            
            {{-- Icon --}}
            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                <div class="{{ $iconColor }}" style="border-radius: 9999px; padding: 0.75rem;">
                    {!! $iconSvg !!}
                </div>
            </div>
            
            {{-- Title --}}
            <h3 style="text-align: center; font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                {{ $title }}
            </h3>
            
            {{-- Message --}}
            <p id="{{ $id }}-message" style="text-align: center; font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem;">
                {{ $message }}
            </p>
            
            {{-- Buttons --}}
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button type="button" id="{{ $id }}-cancel" style="padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: #f3f4f6; border-radius: 0.5rem; border: none; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    {{ $cancelText }}
                </button>
                
                <button type="button" id="{{ $id }}-confirm" class="{{ $buttonColor }}" style="padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; color: white; border-radius: 0.5rem; border: none; cursor: pointer; transition: filter 0.15s;" onmouseover="this.style.filter='brightness(0.9)'" onmouseout="this.style.filter='none'">
                    {{ $confirmText }}
                </button>
            </div>
            
            {{-- Hidden Form --}}
            <form id="{{ $id }}-form" method="POST" action="" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('{{ $id }}');
    const backdrop = document.getElementById('{{ $id }}-backdrop');
    const panel = document.getElementById('{{ $id }}-panel');
    const cancelBtn = document.getElementById('{{ $id }}-cancel');
    const confirmBtn = document.getElementById('{{ $id }}-confirm');
    const form = document.getElementById('{{ $id }}-form');
    const messageEl = document.getElementById('{{ $id }}-message');
    
    let onConfirmCallback = null;
    let onCancelCallback = null;
    
    function show() {
        modal.style.display = 'block';
        // Trigger reflow
        void modal.offsetWidth;
        backdrop.style.opacity = '1';
        panel.style.opacity = '1';
        panel.style.transform = 'scale(1)';
    }
    
    function hide() {
        backdrop.style.opacity = '0';
        panel.style.opacity = '0';
        panel.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }
    
    // Global functions
    window['{{ $functionName }}Show'] = function(options = {}) {
        if (options.message) {
            messageEl.innerHTML = options.message;
        } else {
            messageEl.textContent = `{{ $message }}`;
        }
        
        if (options.formAction) {
            form.action = options.formAction;
            form.innerHTML = '@csrf';
            if (options.formMethod && options.formMethod !== 'POST') {
                form.innerHTML += `@method('${options.formMethod}')`;
            }
        }
        
        onConfirmCallback = options.onConfirm || null;
        onCancelCallback = options.onCancel || null;
        
        show();
    };
    
    window['{{ $functionName }}Hide'] = hide;
    
    // Cancel button
    cancelBtn.addEventListener('click', function() {
        hide();
        if (onCancelCallback) onCancelCallback();
    });
    
    // Confirm button
    confirmBtn.addEventListener('click', function() {
        if (onConfirmCallback) {
            onConfirmCallback();
        } else if (form.action) {
            form.submit();
        }
        hide();
    });
    
    // Backdrop click
    backdrop.addEventListener('click', hide);
    
    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            hide();
        }
    });
})();
</script>
@endpush
