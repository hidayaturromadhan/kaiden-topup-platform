@extends('layouts.admin')

@section('title', 'Kelola Pengguna Internal - KAIDEN')
@section('header_title', 'Manajemen Pengguna Internal & Hak Akses')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Top Action Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search & Filter -->
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 flex-1 max-w-lg">
            <select name="role" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-[#212121]/15 text-xs bg-white text-[#212121]">
                <option value="">Semua Peran</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                @endforeach
            </select>
            <div class="relative w-full sm:flex-1">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau email pengguna..." class="w-full pl-8 pr-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition text-center">
                Cari
            </button>
        </form>

        <!-- Tombol Tambah Pengguna Internal Sesuai RULES.md Section 4.B.4 -->
        <button type="button" onclick="document.getElementById('modal-create-user').classList.remove('hidden')" class="w-full lg:w-auto inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs text-center">
            <x-icon name="user" class="w-3.5 h-3.5" />
            <span>Tambah Pengguna Internal</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4">Status Akun</th>
                        <th class="py-3 px-4">Dibuat Pada</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($users as $u)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <td class="py-3.5 px-4 font-semibold text-[#212121]">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] text-[#6D0808] font-mono font-bold">(Akun Anda)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[#212121]/80">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                @foreach($u->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-mono uppercase {{ $role->name === 'superadmin' ? 'bg-[#6D0808]/15 text-[#6D0808]' : ($role->name === 'admin' ? 'bg-[#212121]/10 text-[#212121]' : 'bg-emerald-50 text-emerald-800') }}">
                                        {{ $role->display_name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 bg-red-50 border border-red-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-[#212121]/60">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('superadmin.users.toggle', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-medium border {{ $u->status === 'aktif' ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} transition">
                                                {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#212121]/50">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna Internal Sesuai RULES.md Section 4.B.4 -->
<div id="modal-create-user" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#212121]/10 shadow-xl">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#212121]">Tambah Pengguna Internal</h3>
            <button type="button" onclick="document.getElementById('modal-create-user').classList.add('hidden')" class="p-1 text-[#212121]/50 hover:text-[#212121]">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label for="name" class="block font-semibold text-[#212121] mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs">
            </div>

            <div>
                <label for="email" class="block font-semibold text-[#212121] mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs">
            </div>

            <div>
                <label for="password" class="block font-semibold text-[#212121] mb-1">Kata Sandi (Min. 8 Karakter)</label>
                <input type="password" name="password" id="password" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs">
            </div>

            <div>
                <label for="role_id" class="block font-semibold text-[#212121] mb-1">Pilih Peran (Role)</label>
                <select name="role_id" id="role_id" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs bg-white">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ $r->name === 'admin' ? 'selected' : '' }}>
                            {{ $r->display_name }} ({{ $r->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block font-semibold text-[#212121] mb-1">Status Akun</label>
                <select name="status" id="status" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs bg-white">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-[#212121]/10">
                <button type="button" onclick="document.getElementById('modal-create-user').classList.add('hidden')" class="px-3.5 py-2 rounded-xl font-semibold border border-[#212121]/15 text-[#212121]">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl font-semibold bg-[#6D0808] text-white hover:bg-[#540606]">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

