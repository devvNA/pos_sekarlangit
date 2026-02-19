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
                <h2 class="text-lg font-bold">Buku Kas</h2>
                <p class="text-sm text-black/60">Pencatatan pemasukan & pengeluaran.</p>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary bg-red-500 hover:bg-amber-600 text-white border-amber-600" type="button"
                    onclick="openCreateModal('out')">Tambah Pengeluaran</button>
                <button class="btn-primary" type="button" onclick="openCreateModal('in')">Tambah Pemasukan</button>
            </div>
        </div>

        <!-- Summary Cards -->
        @if ($entries->count() > 0)
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-green-50 p-3 border border-green-200">
                    <p class="text-xs text-green-700 font-semibold">Total Pemasukan</p>
                    <p class="mt-1 text-lg font-bold text-green-900">Rp {{ number_format($cashIn, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-red-50 p-3 border border-red-200">
                    <p class="text-xs text-red-700 font-semibold">Total Pengeluaran</p>
                    <p class="mt-1 text-lg font-bold text-red-900">Rp {{ number_format($cashOut, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-3 border border-blue-200">
                    <p class="text-xs text-blue-700 font-semibold">Saldo Kas</p>
                    <p class="mt-1 text-lg font-bold {{ $balance >= 0 ? 'text-blue-900' : 'text-red-600' }}">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-xs">
                <thead class="background-color: #1e3a5f; tracking-wide text-black/60">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Referensi</th>
                        <th class="px-4 py-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr class="border-t border-black/10 hover:bg-black/5">
                            <td class="px-4 py-3">{{ $entry->occurred_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if ($entry->type === 'in')
                                    <span
                                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Masuk</span>
                                @else
                                    <span
                                        class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Keluar</span>
                                @endif
                            </td>
                            <td
                                class="px-4 py-3 font-semibold {{ $entry->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($entry->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">{{ $entry->description }}</td>
                            <td class="px-4 py-3 text-black/60">{{ $entry->reference ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                                        onclick='openEditModal(@json($entry))'>
                                        Edit
                                    </button>
                                    <button class="text-xs font-semibold text-red-600 hover:text-red-800"
                                        onclick="confirmDelete({{ $entry->id }}, '{{ addslashes($entry->description) }}')">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-black/10">
                            <td class="px-4 py-4 text-black/60" colspan="6">Belum ada pencatatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal Create -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" id="createModal">
        <div class="w-full max-w-md rounded-2xl border border-black/10 bg-white p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold" id="createModalTitle">Tambah Kas</h3>
                <button class="text-black/60 hover:text-black" onclick="closeCreateModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" action="{{ route('cash.store') }}">
                @csrf

                <input type="hidden" name="type" id="create_type" value="in">
                <input type="hidden" name="occurred_at" id="create_occurred_at_hidden">

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_date">Tanggal
                        <span class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_date" required type="date"
                        value="{{ old('occurred_at') ? substr(old('occurred_at'), 0, 10) : now()->format('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70 mb-1">Waktu <span
                            class="text-red-600">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col">
                            <label class="text-xs text-black/50 mb-1">Jam</label>
                            <select id="create_hour" required
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 pr-8 text-sm appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L0%200h12z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[position:right_0.75rem_center] bg-no-repeat focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @for ($h = 0; $h < 24; $h++)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}"
                                        {{ (old('occurred_at') ? substr(old('occurred_at'), 11, 2) : now()->format('H')) == str_pad($h, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-black/50 mb-1">Menit</label>
                            <select id="create_minute" required
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 pr-8 text-sm appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L0%200h12z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[position:right_0.75rem_center] bg-no-repeat focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @for ($m = 0; $m < 60; $m += 5)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                        {{ (old('occurred_at') ? substr(old('occurred_at'), 14, 2) : (int) (now()->format('i') / 5) * 5) == $m ? 'selected' : '' }}>
                                        {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="create_time"
                        value="{{ old('occurred_at') ? substr(old('occurred_at'), 11, 5) : now()->format('H:i') }}">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_amount">Nominal <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_amount" name="amount" required type="text" data-rupiah inputmode="numeric"
                        value="{{ old('amount') }}" placeholder="0">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_description">Keterangan <span
                            class="text-red-600">*</span></label>
                    <textarea
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_description" name="description" required rows="2" placeholder="Deskripsi transaksi">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_reference">Referensi</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_reference" name="reference" type="text" value="{{ old('reference') }}"
                        placeholder="No. faktur, invoice, dll (opsional)">
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
                <h3 class="text-base font-bold">Edit Catatan Kas</h3>
                <button class="text-black/60 hover:text-black" onclick="closeEditModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_type">Jenis <span
                            class="text-red-600">*</span></label>
                    <select
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 pr-8 text-sm appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L0%200h12z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[position:right_0.75rem_center] bg-no-repeat focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_type" name="type" required>
                        <option value="in">Pemasukan</option>
                        <option value="out">Pengeluaran</option>
                    </select>
                </div>

                <input type="hidden" name="occurred_at" id="edit_occurred_at_hidden">

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_date">Tanggal
                        <span class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_date" required type="date">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70 mb-1">Waktu <span
                            class="text-red-600">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col">
                            <label class="text-xs text-black/50 mb-1">Jam</label>
                            <select id="edit_hour" required
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 pr-8 text-sm appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L0%200h12z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[position:right_0.75rem_center] bg-no-repeat focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @for ($h = 0; $h < 24; $h++)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">
                                        {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-black/50 mb-1">Menit</label>
                            <select id="edit_minute" required
                                class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 pr-8 text-sm appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L0%200h12z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[position:right_0.75rem_center] bg-no-repeat focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @for ($m = 0; $m < 60; $m += 5)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                        {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="edit_time" value="">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_amount">Nominal <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_amount" name="amount" required type="text" data-rupiah inputmode="numeric"
                        placeholder="0">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_description">Keterangan <span
                            class="text-red-600">*</span></label>
                    <textarea
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_description" name="description" required rows="2" placeholder="Deskripsi transaksi"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_reference">Referensi</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_reference" name="reference" type="text"
                        placeholder="No. faktur, invoice, dll (opsional)">
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="btn-secondary flex-1" type="button" onclick="closeEditModal()">Batal</button>
                    <button class="btn-primary flex-1" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <x-confirm-modal id="delete-modal" title="Hapus Catatan Kas" message="Apakah Anda yakin ingin menghapus catatan ini?"
        confirm-text="Ya, Hapus" cancel-text="Batal" confirm-type="danger" icon="warning" />
@endsection

@push('scripts')
    <script>
        // Update time hidden input from hour and minute selects
        function updateTimeInput(hourId, minuteId, timeId) {
            const hour = document.getElementById(hourId).value;
            const minute = document.getElementById(minuteId).value;
            document.getElementById(timeId).value = `${hour}:${minute}`;
        }

        // Setup time picker listeners
        function setupTimePicker(hourId, minuteId, timeId) {
            const hourSelect = document.getElementById(hourId);
            const minuteSelect = document.getElementById(minuteId);

            if (hourSelect && minuteSelect) {
                hourSelect.addEventListener('change', () => updateTimeInput(hourId, minuteId, timeId));
                minuteSelect.addEventListener('change', () => updateTimeInput(hourId, minuteId, timeId));
            }
        }

        // Initialize time pickers
        setupTimePicker('create_hour', 'create_minute', 'create_time');
        setupTimePicker('edit_hour', 'edit_minute', 'edit_time');

        // Combine date and time before form submit
        function combineDateTimeOnSubmit(formId, dateId, timeId, hiddenId, hourId, minuteId) {
            const form = document.querySelector(formId);
            if (form) {
                form.addEventListener('submit', function(e) {
                    const date = document.getElementById(dateId).value;
                    const hour = document.getElementById(hourId).value;
                    const minute = document.getElementById(minuteId).value;
                    if (date && hour && minute) {
                        document.getElementById(hiddenId).value = `${date} ${hour}:${minute}:00`;
                    }
                });
            }
        }

        // Setup form submissions
        combineDateTimeOnSubmit('form[action="{{ route('cash.store') }}"]', 'create_date', 'create_time',
            'create_occurred_at_hidden', 'create_hour', 'create_minute');
        combineDateTimeOnSubmit('#editForm', 'edit_date', 'edit_time', 'edit_occurred_at_hidden', 'edit_hour',
            'edit_minute');

        function openCreateModal(type) {
            document.getElementById('create_type').value = type;
            const modalTitle = type === 'in' ? 'Tambah Pemasukan' : 'Tambah Pengeluaran';
            document.getElementById('createModalTitle').textContent = modalTitle;

            // Reset form
            const now = new Date();
            const dateStr = now.toISOString().split('T')[0];
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(Math.floor(now.getMinutes() / 5) * 5).padStart(2, '0');

            document.getElementById('create_date').value = dateStr;
            document.getElementById('create_hour').value = hours;
            document.getElementById('create_minute').value = minutes;
            document.getElementById('create_time').value = `${hours}:${minutes}`;
            document.getElementById('create_amount').value = '';
            document.getElementById('create_description').value = '';
            document.getElementById('create_reference').value = '';

            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.getElementById('createModal').classList.remove('flex');
        }

        function openEditModal(entry) {
            document.getElementById('edit_type').value = entry.type;

            // Split datetime into date and time
            const date = new Date(entry.occurred_at);
            const dateStr = date.toISOString().split('T')[0];
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(Math.floor(date.getMinutes() / 5) * 5).padStart(2, '0');

            document.getElementById('edit_date').value = dateStr;
            document.getElementById('edit_hour').value = hours;
            document.getElementById('edit_minute').value = minutes;
            document.getElementById('edit_time').value = `${hours}:${minutes}`;

            document.getElementById('edit_amount').value = formatRupiahValue(entry.amount);
            document.getElementById('edit_description').value = entry.description;
            document.getElementById('edit_reference').value = entry.reference || '';
            document.getElementById('editForm').action = `/kas/${entry.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        function confirmDelete(id, description) {
            deleteModalShow({
                message: `Hapus catatan "<strong>${description}</strong>"?`,
                formAction: `/kas/${id}`,
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

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
            }
        });

        // Auto open create modal if validation errors exist
        @if ($errors->any() && old('_method') === null)
            const oldType = "{{ old('type', 'in') }}";
            openCreateModal(oldType);
        @endif
    </script>
@endpush
