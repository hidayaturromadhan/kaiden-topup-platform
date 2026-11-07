@extends('layouts.admin')

@section('title', 'Peran & Matriks Izin RBAC - KAIDEN')
@section('header_title', 'Tata Kelola RBAC (Role-Based Access Control)')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Overview Box -->
    <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-2">
        <h2 class="text-sm font-bold text-[#212121]">Matriks Otorisasi Berbasis Izin (Permission-Based RBAC)</h2>
        <p class="text-xs text-[#212121]/70 leading-relaxed">
            Sistem KAIDEN menerapkan prinsip <em>Least Privilege</em>. Hak akses diverifikasi di level controller dan middleware sisi server. Setiap perubahan matriks izin akan dicatat secara transparan pada Audit Log.
        </p>
    </div>

    <!-- Roles Grid -->
    <div class="space-y-6">
        @foreach($roles as $role)
            <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-[#212121]/10 bg-[#F9F8F6] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-bold text-[#212121]">{{ $role->display_name }}</h3>
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-[#212121]/5 text-[#212121]/80">({{ $role->name }})</span>
                        </div>
                        <p class="text-xs text-[#212121]/60 mt-0.5">{{ $role->description }}</p>
                    </div>

                    <div>
                        <span class="text-xs font-mono font-semibold text-[#6D0808]">
                            {{ $role->permissions->count() }} Izin Terpasang
                        </span>
                    </div>
                </div>

                <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST" class="p-4 sm:p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        @foreach($permissions as $group => $perms)
                            <div class="p-3.5 rounded-xl border border-[#212121]/10 bg-[#F9F8F6]/40 space-y-2 text-xs">
                                <div class="font-bold uppercase tracking-wider text-[11px] text-[#212121] pb-1 border-b border-[#212121]/10">
                                    Modul: {{ $group }}
                                </div>
                                <div class="space-y-1.5 pt-1">
                                    @foreach($perms as $p)
                                        <label class="flex items-start space-x-2 cursor-pointer select-none">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" {{ $role->permissions->contains('id', $p->id) ? 'checked' : '' }} class="mt-0.5 rounded border-[#212121]/20 text-[#6D0808] focus:ring-[#6D0808]">
                                            <div>
                                                <div class="font-semibold text-[#212121]">{{ $p->display_name }}</div>
                                                <div class="text-[10px] font-mono text-[#212121]/50">{{ $p->name }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-[#212121]/10 flex justify-stretch sm:justify-end">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs text-center">
                            <x-icon name="check" class="w-3.5 h-3.5" />
                            <span>Perbarui Izin Peran '{{ $role->display_name }}'</span>
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection

