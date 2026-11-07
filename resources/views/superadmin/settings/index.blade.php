@extends('layouts.admin')

@section('title', 'Kredensial API & Feature Flag - KAIDEN')
@section('header_title', 'Konfigurasi Integrasi & Kunci API')

@section('content')
<div class="space-y-6 pt-2 max-w-4xl">

    <!-- Notice Box Sesuai RULES.md Section 3 no. 9 -->
    <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-start space-x-3 text-xs">
        <x-icon name="shield-check" class="w-5 h-5 text-[#6D0808] shrink-0 mt-0.5" />
        <div class="space-y-1">
            <h3 class="font-bold text-[#212121]">Keamanan Penyimpanan Kredensial</h3>
            <p class="text-[#212121]/70 leading-relaxed">
                Seluruh nilai kunci API yang disimpan ke basis data dienkripsi secara otomatis menggunakan algoritma <code>AES-256-CBC</code> (<code>Crypt::encryptString</code>). Perubahan konfigurasi dicatat ke dalam <strong>Audit Log</strong> tanpa membocorkan nilai rahasia.
            </p>
        </div>
    </div>

    <!-- Form Settings -->
    <form action="{{ route('superadmin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Midtrans Payment Gateway -->
        <div class="p-4 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-[#212121]/10">
                <div class="w-7 h-7 rounded-lg bg-[#212121] text-white flex items-center justify-center font-bold text-xs">
                    M
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Gerbang Pembayaran Midtrans</h3>
                    <p class="text-xs text-[#212121]/60">Pengaturan integrasi Snap Midtrans untuk transaksi pelanggan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1.5">
                    <label for="midtrans_server_key" class="block font-semibold text-[#212121]">
                        Server Key (Terenkripsi)
                    </label>
                    <input type="password" name="midtrans_server_key" id="midtrans_server_key" placeholder="{{ !empty($settings['midtrans_server_key']) ? '••••••••••••••••••••••••' : 'Masukkan Server Key' }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                    <p class="text-[10px] text-[#212121]/50">Kosongkan jika tidak ingin mengubah kunci saat ini.</p>
                </div>

                <div class="space-y-1.5">
                    <label for="midtrans_client_key" class="block font-semibold text-[#212121]">
                        Client Key (Publik)
                    </label>
                    <input type="text" name="midtrans_client_key" id="midtrans_client_key" value="{{ $settings['midtrans_client_key'] }}" placeholder="SB-Mid-client-..." class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label for="midtrans_is_production" class="block font-semibold text-[#212121]">
                        Mode Lingkungan (Environment)
                    </label>
                    <select name="midtrans_is_production" id="midtrans_is_production" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
                        <option value="false" {{ $settings['midtrans_is_production'] === 'false' || !$settings['midtrans_is_production'] ? 'selected' : '' }}>
                            Sandbox / Development Mode (Pengujian)
                        </option>
                        <option value="true" {{ $settings['midtrans_is_production'] === 'true' ? 'selected' : '' }}>
                            Production Mode (Live Transaksi Nyata)
                        </option>
                    </select>
                </div>

                <div class="sm:col-span-2 p-3 rounded-xl bg-[#F9F8F6] border border-[#212121]/10 space-y-1">
                    <span class="text-[11px] font-bold text-[#212121]">Notification Webhook URL (Salin ke Dashboard Midtrans):</span>
                    <div class="font-mono text-xs text-[#6D0808] select-all bg-white px-2.5 py-1.5 rounded-lg border border-[#212121]/10">
                        {{ url('/midtrans/notification') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Digiflazz Provider -->
        <div class="p-4 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-[#212121]/10">
                <div class="w-7 h-7 rounded-lg bg-[#6D0808] text-white flex items-center justify-center font-bold text-xs">
                    D
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Pemenuhan Pesanan Digiflazz</h3>
                    <p class="text-xs text-[#212121]/60">Pengaturan API pengiriman voucher dan pemantauan saldo deposit.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1.5">
                    <label for="digiflazz_username" class="block font-semibold text-[#212121]">Username Digiflazz</label>
                    <input type="text" name="digiflazz_username" id="digiflazz_username" value="{{ $settings['digiflazz_username'] }}" placeholder="Username akun Digiflazz" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="digiflazz_api_key" class="block font-semibold text-[#212121]">API Key (Terenkripsi)</label>
                    <input type="password" name="digiflazz_api_key" id="digiflazz_api_key" placeholder="{{ !empty($settings['digiflazz_api_key']) ? '••••••••••••••••••••••••' : 'Masukkan API Key Digiflazz' }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                    <p class="text-[10px] text-[#212121]/50">Kosongkan jika tidak ingin mengubah kunci saat ini.</p>
                </div>

                <div class="space-y-1.5">
                    <label for="digiflazz_base_url" class="block font-semibold text-[#212121]">Base URL API</label>
                    <input type="url" name="digiflazz_base_url" id="digiflazz_base_url" value="{{ $settings['digiflazz_base_url'] }}" required class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="digiflazz_low_balance_threshold" class="block font-semibold text-[#212121]">
                        Ambang Batas Saldo Rendah (Rp)
                    </label>
                    <input type="number" name="digiflazz_low_balance_threshold" id="digiflazz_low_balance_threshold" value="{{ $settings['digiflazz_low_balance_threshold'] }}" required step="10000" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono tabular-nums text-xs">
                    <p class="text-[10px] text-[#212121]/50">Memicu warning badge merah di dashboard admin jika saldo berada di bawah angka ini.</p>
                </div>

                <div class="sm:col-span-2 p-3 rounded-xl bg-[#F9F8F6] border border-[#212121]/10 space-y-1">
                    <span class="text-[11px] font-bold text-[#212121]">Callback Webhook URL (Salin ke Dashboard Digiflazz):</span>
                    <div class="font-mono text-xs text-[#6D0808] select-all bg-white px-2.5 py-1.5 rounded-lg border border-[#212121]/10">
                        {{ url('/digiflazz/webhook') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- IGN Checker Feature Flag Sesuai RULES.md Section 5.A.3 -->
        <div class="p-4 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-[#212121]/10">
                <div class="w-7 h-7 rounded-lg bg-[#212121] text-white flex items-center justify-center font-bold text-xs">
                    <x-icon name="gamepad" class="w-4 h-4 text-[#F9F8F6]" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Feature Flag: Pengecekan Nickname Game (IGN Checker)</h3>
                    <p class="text-xs text-[#212121]/60">Kontrol aktivasi inquiry nickname otomatis via GameCheckService (GoPay Games provider).</p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label for="ign_checker_enabled" class="block font-semibold text-[#212121] mb-1">Status Fitur Pengecekan Nickname</label>
                    <select name="ign_checker_enabled" id="ign_checker_enabled" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs">
                        <option value="true" {{ $settings['ign_checker_enabled'] === 'true' || $settings['ign_checker_enabled'] === true ? 'selected' : '' }}>
                            Aktif (Pengecekan Otomatis Berjalan)
                        </option>
                        <option value="false" {{ $settings['ign_checker_enabled'] === 'false' || $settings['ign_checker_enabled'] === false ? 'selected' : '' }}>
                            Nonaktif (Gunakan Fallback Konfirmasi Manual Secara Penuh)
                        </option>
                    </select>
                </div>
                <p class="text-[11px] text-[#212121]/60 leading-relaxed">
                    Sesuai PRD Bab 2 & RULES.md Section 5.A, jika provider GoPay mengalami pemblokiran atau perubahan endpoint, Superadmin dapat menonaktifkan fitur ini sewaktu-waktu tanpa mengganggu jalannya transaksi pelanggan.
                </p>
            </div>
        </div>

        <!-- Customer Care WhatsApp Settings -->
        <div class="p-4 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-[#212121]/10">
                <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs">
                    <x-icon name="headphones" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Layanan Bantuan Pelanggan (WhatsApp wa.me)</h3>
                    <p class="text-xs text-[#212121]/60">Nomor WhatsApp resmi yang dituju saat pelanggan menekan tombol Bantuan atau Customer Care.</p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label for="whatsapp_number" class="block font-semibold text-[#212121] mb-1">
                        Nomor WhatsApp CS (Format Internasional Tanpa Tanda +, Contoh: 6281234567890)
                    </label>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '6281234567890' }}" required placeholder="628xxxxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 font-mono text-xs">
                    <p class="text-[10px] text-[#212121]/50 mt-1">
                        Nomor ini akan digunakan untuk tautan <code>https://wa.me/{nomor}</code> di navbar, hero section, footer, dan halaman status pesanan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                <x-icon name="check" class="w-4 h-4" />
                <span>Simpan Konfigurasi & Catat Audit Log</span>
            </button>
        </div>
    </form>
</div>
@endsection

