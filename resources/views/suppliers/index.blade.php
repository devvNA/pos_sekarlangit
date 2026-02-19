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
                <h2 class="text-lg font-bold">Pemasok</h2>
                <p class="text-sm text-black/60">Daftar pemasok dan kontak.</p>
            </div>
            <button class="btn-primary" type="button" onclick="openCreateModal()">Tambah Pemasok</button>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-xs">
                <thead class="background-color: #1e3a5f; tracking-wide text-black/60">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr class="border-t border-black/10 hover:bg-black/5">
                            <td class="px-4 py-3 font-medium">{{ $supplier->name }}</td>
                            <td class="px-4 py-3">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-4 py-3">{{ Str::limit($supplier->address ?? '-', 50) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                                        onclick='openEditModal(@json($supplier))'>
                                        Edit
                                    </button>
                                    <button class="text-xs font-semibold text-red-600 hover:text-red-800"
                                        onclick="confirmDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-black/10">
                            <td class="px-4 py-4 text-black/60" colspan="4">Belum ada pemasok.</td>
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
                <h3 class="text-base font-bold">Tambah Pemasok</h3>
                <button class="text-black/60 hover:text-black" onclick="closeCreateModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" action="{{ route('suppliers.store') }}">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_name">Nama Pemasok <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_name" name="name" required type="text" value="{{ old('name') }}"
                        placeholder="PT. ABC">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_phone">Telepon</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_phone" name="phone" type="text" value="{{ old('phone') }}"
                        placeholder="08123456789">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="create_address">Alamat</label>
                    <textarea
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="create_address" name="address" rows="3" placeholder="Jl. Contoh No. 123">{{ old('address') }}</textarea>
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
                <h3 class="text-base font-bold">Edit Pemasok</h3>
                <button class="text-black/60 hover:text-black" onclick="closeEditModal()">&times;</button>
            </div>

            <form class="mt-4 space-y-3" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_name">Nama Pemasok <span
                            class="text-red-600">*</span></label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_name" name="name" required type="text" placeholder="PT. ABC">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_phone">Telepon</label>
                    <input
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_phone" name="phone" type="text" placeholder="08123456789">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-black/70" for="edit_address">Alamat</label>
                    <textarea
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        id="edit_address" name="address" rows="3" placeholder="Jl. Contoh No. 123"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="btn-secondary flex-1" type="button" onclick="closeEditModal()">Batal</button>
                    <button class="btn-primary flex-1" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <x-confirm-modal id="delete-modal" title="Hapus Pemasok" message="Apakah Anda yakin ingin menghapus pemasok ini?"
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

        function openEditModal(supplier) {
            document.getElementById('edit_name').value = supplier.name;
            document.getElementById('edit_phone').value = supplier.phone || '';
            document.getElementById('edit_address').value = supplier.address || '';
            document.getElementById('editForm').action = `/pemasok/${supplier.id}`;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        function confirmDelete(id, name) {
            deleteModalShow({
                message: `Hapus pemasok "<strong>${name}</strong>"?<br><small class="text-black/60">Pemasok dengan produk terkait tidak dapat dihapus.</small>`,
                formAction: `/pemasok/${id}`,
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
            openCreateModal();
        @endif

        // Auto open edit modal if validation errors exist for update
        @if ($errors->any() && old('_method') === 'PUT')
            // We need to get supplier data from old input
            // This is a fallback, ideally we'd pass the supplier data back
            console.log('Edit form has validation errors');
        @endif
    </script>
@endpush
