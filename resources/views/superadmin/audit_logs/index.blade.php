@extends('layouts.admin')

@section('title', 'Audit Log (Append-Only) - KAIDEN')
@section('header_title', 'Catatan Audit Aktivitas Sistem (Append-Only)')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Notice Box Sesuai RULES.md Section 4.C.1 -->
    <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-start space-x-3 text-xs">
        <x-icon name="shield" class="w-5 h-5 text-[#6D0808] shrink-0 mt-0.5" />
        <div class="space-y-1">
            <h3 class="font-bold text-[#212121]">Integritas Data Audit Log (Append-Only & Immutable)</h3>
            <p class="text-[#212121]/70 leading-relaxed">
                Tabel <code>audit_logs</code> bersifat kekal (append-only). Model aplikasi melarang keras operasi <code>update()</code> dan <code>delete()</code>. Seluruh perubahan harga, otorisasi peran, integrasi, dan login staf internal terdokumentasi secara transparan.
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs">
        <form action="{{ route('superadmin.audit_logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end text-xs">
            <div>
                <label for="actor_id" class="block font-semibold text-[#212121]/70 mb-1">Pelaku (Actor)</label>
                <select name="actor_id" id="actor_id" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
                    <option value="">Semua Staf</option>
                    @foreach($actors as $act)
                        <option value="{{ $act->id }}" {{ $actorId == $act->id ? 'selected' : '' }}>
                            {{ $act->name }} ({{ $act->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="action" class="block font-semibold text-[#212121]/70 mb-1">Aksi</label>
                <select name="action" id="action" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
                    <option value="">Semua Aksi</option>
                    @foreach($distinctActions as $actName)
                        <option value="{{ $actName }}" {{ $action === $actName ? 'selected' : '' }}>
                            {{ $actName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block font-semibold text-[#212121]/70 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
            </div>

            <div>
                <label for="end_date" class="block font-semibold text-[#212121]/70 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
            </div>

            <div class="sm:col-span-2 lg:col-span-4 pt-1">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl font-semibold text-white bg-[#212121] hover:bg-black transition text-center">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Pelaku (Actor)</th>
                        <th class="py-3 px-4">Aksi Tercatat</th>
                        <th class="py-3 px-4">Target Objek</th>
                        <th class="py-3 px-4">Alamat IP</th>
                        <th class="py-3 px-4 text-right">Detail Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-[#212121]/60">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#212121]">{{ $log->actor->name ?? 'Sistem / Anonim' }}</div>
                                <div class="text-[10px] text-[#212121]/50 font-mono">{{ $log->actor->email ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded bg-[#212121]/5 text-[#6D0808]">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#212121]/80 font-mono text-[11px]">
                                {{ $log->target_type ? $log->target_type . ' #' . ($log->target_id ?? '-') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono tabular-nums text-[#212121]/70">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('superadmin.audit_logs.show', $log->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#212121] bg-[#F9F8F6] border border-[#212121]/15 hover:bg-[#212121]/5 transition">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                    <span>Periksa Diff</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#212121]/50">
                                Belum ada catatan log aktivitas yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

