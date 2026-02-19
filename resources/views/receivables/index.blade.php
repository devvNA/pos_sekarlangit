@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl border border-black/10 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-bold">Buku Piutang</h2>
                <p class="text-sm text-black/60">Catat hutang pelanggan dan pembayaran.</p>
            </div>
            <button class="btn-primary" type="button" onclick="openCreateModal()">Tambah Piutang</button>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-xs">
                <thead class="background-color: #1e3a5f; tracking-wide text-black/60">
                    <tr>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Dibayar</th>
                        <th class="px-4 py-3">Sisa</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receivables as $receivable)
                        @php
                            $totalPaid = $receivable->payments->sum('amount');
                            $isLunas = $receivable->status === 'lunas';
                            $isOverdue = $receivable->due_date && $receivable->due_date->isPast() && !$isLunas;
                        @endphp
                        <tr class="border-t border-black/10 hover:bg-black/5">
                            <td class="px-4 py-3 font-medium">{{ $receivable->customer_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($receivable->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-green-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-semibold {{ $isLunas ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($receivable->remaining, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($receivable->due_date)
                                    <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $receivable->due_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-black/40">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($isLunas)
                                    <span
                                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Lunas</span>
                                @elseif ($isOverdue)
                                    <span
                                        class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Jatuh
                                        Tempo</span>
                                @else
                                    <span
                                        class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Belum
                                        Lunas</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-800"
                                        onclick='openPaymentModal(@json($receivable))'>
                                        {{ $isLunas ? 'Detail' : 'Bayar' }}
                                    </button>
                                    <button class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                                        onclick='openEditModal(@json($receivable))'>
                                        Edit
                                    </button>
                                    <button class="text-xs font-semibold text-red-600 hover:text-red-800"
                                        onclick="confirmDelete({{ $receivable->id }}, '{{ addslashes($receivable->customer_name) }}')">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-black/10">
                            <td class="px-4 py-4 text-black/60" colspan="7">Belum ada piutang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($receivables->count() > 0)
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-blue-50 p-3 border border-blue-200">
                    <p class="text-xs text-blue-700 font-semibold">Total Piutang</p>
                    <p class="mt-1 text-lg font-bold text-blue-900">Rp
                        {{ number_format($receivables->sum('total'), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-green-50 p-3 border border-green-200">
                    <p class="text-xs text-green-700 font-semibold">Total Terbayar</p>
                    <p class="mt-1 text-lg font-bold text-green-900">Rp
                        {{ number_format($receivables->sum(fn($r) => $r->payments->sum('amount')), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-red-50 p-3 border border-red-200">
                    <p class="text-xs text-red-700 font-semibold">Sisa Piutang</p>
                    <p class="mt-1 text-lg font-bold text-red-900">Rp
                        {{ number_format($receivables->where('status', 'belum_lunas')->sum('remaining'), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @endif
    </section>

    <!-- Modal Create -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" id="createModal">
        <div class="w-full max-w-md rounded-2xl border border-black/10 bg-white p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold">Tambah Piutang</h3>
                <button class="text-black/60 hover:text-black" onclick="closeCreateModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" action="{{ route('receivables.store') }}">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_customer_id">Pilih Pelanggan
                        (Opsional)</label>
                    <select
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_customer_id" name="customer_id" onchange="updateCustomerName(this)">
                        <option value="">-- Pilih atau ketik manual --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_customer_name">Nama Pelanggan
                        <span class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_customer_name" name="customer_name" required type="text"
                        value="{{ old('customer_name') }}" placeholder="Nama pelanggan">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_total">Total Piutang <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_total" name="total" required type="text" data-rupiah inputmode="numeric"
                        value="{{ old('total') }}" placeholder="0">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_due_date">Jatuh Tempo</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_due_date" name="due_date" type="date" value="{{ old('due_date') }}">
                    <p class="mt-1 text-xs text-black/50">Kosongkan jika tidak ada batas waktu</p>
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="btn-secondary flex-1" type="button" onclick="closeCreateModal()">Batal</button>
                    <button class="btn-primary flex-1" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" id="editModal">
        <div class="w-full max-w-md rounded-2xl border border-black/10 bg-white p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold">Edit Piutang</h3>
                <button class="text-black/60 hover:text-black" onclick="closeEditModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_customer_id">Pilih Pelanggan
                        (Opsional)</label>
                    <select
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_customer_id" name="customer_id" onchange="updateEditCustomerName(this)">
                        <option value="">-- Pilih atau ketik manual --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_customer_name">Nama Pelanggan
                        <span class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_customer_name" name="customer_name" required type="text"
                        placeholder="Nama pelanggan">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_total">Total Piutang <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_total" name="total" required type="text" data-rupiah inputmode="numeric"
                        placeholder="0">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_due_date">Jatuh Tempo</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_due_date" name="due_date" type="date">
                    <p class="mt-1 text-xs text-black/50">Kosongkan jika tidak ada batas waktu</p>
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="btn-secondary flex-1" type="button" onclick="closeEditModal()">Batal</button>
                    <button class="btn-primary flex-1" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Payment/Detail -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" id="paymentModal">
        <div
            class="w-full max-w-2xl rounded-2xl border border-black/10 bg-white p-5 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold">Detail & Pembayaran Piutang</h3>
                <button class="text-black/60 hover:text-black text-lg" onclick="closePaymentModal()">&times;</button>
            </div>

            <!-- Detail Section -->
            <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 border border-blue-200">
                <div class="grid grid-cols-2 gap-4">
                    <div class="pb-3 border-b border-blue-200">
                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Pelanggan</p>
                        <p class="font-bold text-base mt-1" id="payment_customer_name">-</p>
                    </div>
                    <div class="pb-3 border-b border-blue-200">
                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Piutang</p>
                        <p class="font-bold text-base mt-1" id="payment_total">-</p>
                    </div>
                    <div class="pt-3">
                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Sisa Piutang</p>
                        <p class="font-bold text-base text-red-600 mt-1" id="payment_remaining">-</p>
                    </div>
                    <div class="pt-3">
                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Status</p>
                        <p class="mt-1" id="payment_status">-</p>
                    </div>
                </div>
            </div>

            <!-- Payment History Section -->
            <div class="mt-5">
                <h4 class="text-xs font-bold text-black/70 uppercase tracking-wider mb-3">Riwayat Pembayaran</h4>
                <div class="overflow-hidden rounded-lg border border-black/10 bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="background-color: #1e3a5f; border-b border-black/10">
                            <tr>
                                <th class="px-4 py-3 font-semibold ">Tanggal</th>
                                <th class="px-4 py-3 font-semibold ">Jumlah</th>
                                <th class="px-4 py-3 font-semibold ">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="payment_history">
                            <tr>
                                <td class="px-4 py-4 text-black/60" colspan="3">Belum ada pembayaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Payment Form Section -->
            <div class="mt-6" id="add_payment_section">
                <h4 class="text-xs font-bold text-black/70 uppercase tracking-wider mb-3">Tambah Pembayaran</h4>
                <form class="space-y-4" method="POST" id="paymentForm">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-black/70 mb-2" for="payment_amount">Jumlah
                                Bayar
                                <span class="text-red-600">*</span></label>
                            <input
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                id="payment_amount" name="amount" required type="text" data-rupiah
                                inputmode="numeric" placeholder="Rp 0">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-black/70 mb-2" for="payment_paid_at">Tanggal
                                Bayar
                                <span class="text-red-600">*</span></label>
                            <input
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                id="payment_paid_at" name="paid_at" required type="date"
                                value="{{ date('Y-m-d') }}">
                            <p class="mt-1 text-xs text-black/50">Default: Hari ini</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-black/70 mb-2"
                            for="payment_note">Keterangan</label>
                        <input
                            class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            id="payment_note" name="note" type="text" placeholder="Catatan pembayaran (opsional)">
                    </div>

                    <div class="flex gap-3 pt-3 border-t border-black/10">
                        <button class="btn-secondary flex-1" type="button" onclick="closePaymentModal()">Tutup</button>
                        <button class="btn-primary flex-1" type="submit">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <x-confirm-modal id="delete-modal" title="Hapus Piutang" message="Apakah Anda yakin ingin menghapus piutang ini?"
        confirm-text="Ya, Hapus" cancel-text="Batal" confirm-type="danger" icon="warning" />
@endsection

@push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.getElementById('createModal').classList.remove('flex');
        }

        function updateCustomerName(select) {
            const selectedOption = select.options[select.selectedIndex];
            const customerName = selectedOption.getAttribute('data-name');
            if (customerName) {
                document.getElementById('create_customer_name').value = customerName;
            }
        }

        function updateEditCustomerName(select) {
            const selectedOption = select.options[select.selectedIndex];
            const customerName = selectedOption.getAttribute('data-name');
            if (customerName) {
                document.getElementById('edit_customer_name').value = customerName;
            }
        }

        function openEditModal(receivable) {
            document.getElementById('edit_customer_id').value = receivable.customer_id || '';
            document.getElementById('edit_customer_name').value = receivable.customer_name;
            document.getElementById('edit_total').value = formatRupiahValue(receivable.total);
            document.getElementById('edit_due_date').value = receivable.due_date || '';
            document.getElementById('editForm').action = `/piutang/${receivable.id}`;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        function openPaymentModal(receivable) {
            // Populate detail
            document.getElementById('payment_customer_name').textContent = receivable.customer_name;
            document.getElementById('payment_total').textContent = 'Rp ' + Number(receivable.total).toLocaleString('id-ID');
            document.getElementById('payment_remaining').textContent = 'Rp ' + Number(receivable.remaining).toLocaleString(
                'id-ID');

            const isLunas = receivable.status === 'lunas';
            document.getElementById('payment_status').innerHTML = isLunas ?
                '<span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Lunas</span>' :
                '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Belum Lunas</span>';

            // Populate payment history
            const historyBody = document.getElementById('payment_history');
            if (receivable.payments && receivable.payments.length > 0) {
                historyBody.innerHTML = receivable.payments.map(payment => `
                    <tr class="border-t border-black/10">
                        <td class="px-4 py-3">${new Date(payment.paid_at).toLocaleDateString('id-ID')}</td>
                        <td class="px-4 py-3 font-semibold text-green-600">Rp ${Number(payment.amount).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-3">${payment.note || '-'}</td>
                    </tr>
                `).join('');
            } else {
                historyBody.innerHTML =
                    '<tr><td class="px-4 py-4 text-black/60" colspan="3">Belum ada pembayaran.</td></tr>';
            }

            // Show/hide add payment form
            const addPaymentSection = document.getElementById('add_payment_section');
            if (isLunas) {
                addPaymentSection.style.display = 'none';
            } else {
                addPaymentSection.style.display = 'block';
            }

            // Set form action
            document.getElementById('paymentForm').action = `/piutang/${receivable.id}/bayar`;

            // Reset form
            document.getElementById('payment_amount').value = '';
            document.getElementById('payment_note').value = '';

            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.remove('flex');
        }

        function confirmDelete(id, name) {
            deleteModalShow({
                message: `Hapus piutang atas nama "<strong>${name}</strong>"?<br><small class="text-black/60">Piutang dengan pembayaran tidak dapat dihapus.</small>`,
                formAction: `/piutang/${id}`,
                formMethod: 'DELETE'
            });
        }

        // Close modal on outside click
        document.getElementById('createModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });

        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        document.getElementById('paymentModal')?.addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
                closePaymentModal();
            }
        });

        // Auto open create modal if validation errors exist
        @if ($errors->any() && old('_method') === null && !request()->has('receivable'))
            openCreateModal();
        @endif
    </script>
@endpush
