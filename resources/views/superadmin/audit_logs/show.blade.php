@extends('layouts.admin')

@section('title', 'Detail Catatan Audit #' . $auditLog->id . ' - KAIDEN')
@section('header_title', 'Inspeksi Detail Audit Log #' . $auditLog->id)

@section('content')
<div class="space-y-6 pt-2 max-w-4xl">

    <div>
        <a href="{{ route('superadmin.audit_logs.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#212121]/70 hover:text-[#6D0808] transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Daftar Audit Log</span>
        </a>
    </div>

    <!-- Metadata Card -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-[#212121]/10 bg-[#F9F8F6] flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <div class="text-xs text-[#212121]/60">Aksi Audit</div>
                <div class="font-mono text-base font-bold text-[#6D0808]">{{ $auditLog->action }}</div>
                <div class="text-[11px] text-[#212121]/50 mt-0.5">{{ $auditLog->created_at->format('d F Y, H:i:s') }} WIB</div>
            </div>

            <div class="sm:text-right">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-[#212121]/5 text-[#212121] border border-[#212121]/10">
                    ID Rekaman: #{{ $auditLog->id }}
                </span>
            </div>
        </div>

        <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border-b border-[#212121]/10">
            <div>
                <span class="text-[#212121]/60 block mb-0.5">Pelaku (Actor):</span>
                <span class="font-bold text-[#212121]">{{ $auditLog->actor->name ?? 'Sistem / Tamu' }}</span>
                <span class="block text-[11px] text-[#212121]/60 font-mono">{{ $auditLog->actor->email ?? '-' }}</span>
            </div>

            <div>
                <span class="text-[#212121]/60 block mb-0.5">Target Objek:</span>
                <span class="font-mono font-bold text-[#212121]">{{ $auditLog->target_type ?? 'Sistem Global' }} {{ $auditLog->target_id ? '#' . $auditLog->target_id : '' }}</span>
            </div>

            <div>
                <span class="text-[#212121]/60 block mb-0.5">Alamat IP Pelaku:</span>
                <span class="font-mono tabular-nums font-semibold text-[#212121]">{{ $auditLog->ip_address ?? '127.0.0.1' }}</span>
            </div>

            <div>
                <span class="text-[#212121]/60 block mb-0.5">User Agent:</span>
                <span class="font-mono text-[10px] text-[#212121]/70 break-all">{{ $auditLog->user_agent ?? 'Unknown' }}</span>
            </div>
        </div>

        <!-- JSON Diff Area -->
        <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 bg-[#F9F8F6]/30">
            <!-- Data Sebelum (old_values) -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#212121] flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#212121]/40"></span>
                    <span>Nilai Sebelum (Old Values)</span>
                </h4>
                <div class="p-3.5 rounded-xl bg-[#212121] text-[#F9F8F6] font-mono text-xs overflow-x-auto min-h-[140px]">
                    <pre>{{ $auditLog->old_values ? json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '// Tidak ada data sebelumnya (Record Baru)' }}</pre>
                </div>
            </div>

            <!-- Data Sesudah (new_values) -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#6D0808] flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#6D0808]"></span>
                    <span>Nilai Sesudah (New Values)</span>
                </h4>
                <div class="p-3.5 rounded-xl bg-[#212121] text-[#F9F8F6] font-mono text-xs overflow-x-auto min-h-[140px]">
                    <pre>{{ $auditLog->new_values ? json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '// Tidak ada nilai baru' }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

