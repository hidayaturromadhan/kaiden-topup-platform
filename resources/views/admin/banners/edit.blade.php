@extends('layouts.admin')

@section('title', 'Ubah Banner Promo - KAIDEN')
@section('header_title', 'Ubah Banner Promo')

@section('content')
<div class="max-w-4xl space-y-6 pt-2">

    <!-- Header & Breadcrumb -->
    <div>
        <div class="flex items-center space-x-2 text-xs text-[#212121]/50 mb-1">
            <a href="{{ route('admin.banners.index') }}" class="hover:text-[#6D0808] transition">Banner &amp; Promo</a>
            <x-icon name="chevron-right" class="w-3 h-3" />
            <span class="text-[#212121] font-semibold">Ubah Banner #{{ $banner->id }}</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-[#212121]">Ubah Banner Promo</h1>
        <p class="text-xs text-[#212121]/60 mt-1">Perbarui konten promosi, tautan diskon, atau ganti visual poster.</p>
    </div>

    <!-- Form Ubah Banner -->
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Tipe & Visual -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-[#212121] uppercase tracking-wider flex items-center space-x-2 pb-3 border-b border-[#212121]/10">
                <x-icon name="info" class="w-4 h-4 text-[#6D0808]" />
                <span>1. Tipe &amp; Visual Poster Banner</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tipe Banner -->
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1.5">Tipe Penempatan Banner <span class="text-red-600">*</span></label>
                    <select name="banner_type" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                        <option value="slider" {{ old('banner_type', $banner->banner_type) === 'slider' ? 'selected' : '' }}>Slider Utama Beranda (Kolom Besar 8)</option>
                        <option value="side" {{ old('banner_type', $banner->banner_type) === 'side' ? 'selected' : '' }}>Side Banner Promosi (Kolom Samping 4)</option>
                    </select>
                </div>

                <!-- Tema Warna Background Gradient -->
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1.5">Tema Warna Gradien <span class="text-red-600">*</span></label>
                    <select name="gradient_theme" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                        <option value="crimson" {{ old('gradient_theme', $banner->gradient_theme) === 'crimson' ? 'selected' : '' }}>KAIDEN Crimson (#6D0808 - Merah Elegan)</option>
                        <option value="slate" {{ old('gradient_theme', $banner->gradient_theme) === 'slate' ? 'selected' : '' }}>Midnight Slate (#1E293B - Gelap Taktikal)</option>
                        <option value="emerald" {{ old('gradient_theme', $banner->gradient_theme) === 'emerald' ? 'selected' : '' }}>Emerald Jade (#064E3B - Hijau Verifikasi)</option>
                        <option value="amber" {{ old('gradient_theme', $banner->gradient_theme) === 'amber' ? 'selected' : '' }}>Imperial Amber (#78350F - Emas Eksklusif)</option>
                        <option value="blue" {{ old('gradient_theme', $banner->gradient_theme) === 'blue' ? 'selected' : '' }}>Royal Blue (#1E3A8A - Biru Komersial)</option>
                    </select>
                </div>
            </div>

            <!-- Preview Poster Saat Ini -->
            @if($banner->image && file_exists(public_path($banner->image)))
                <div class="p-3.5 rounded-xl bg-[#F9F8F6] border border-[#212121]/10 flex items-center space-x-4">
                    <img src="{{ asset($banner->image) }}" alt="Preview Poster" class="w-24 h-14 object-cover rounded-lg border border-[#212121]/15">
                    <div>
                        <div class="text-xs font-bold text-[#212121]">Visual Poster Saat Ini</div>
                        <div class="text-[10px] text-[#212121]/50 font-mono">{{ $banner->image }}</div>
                    </div>
                </div>
            @endif

            <!-- Unggah Gambar Baru -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-[#212121]">
                    Ganti Poster dengan File Baru (Opsional)
                </label>
                <div class="flex items-center space-x-3">
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-[#212121]/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#212121]/5 file:text-[#212121] hover:file:bg-[#212121]/10 transition cursor-pointer bg-[#F9F8F6] border border-[#212121]/15 rounded-xl p-1">
                </div>
                <p class="text-[11px] text-[#212121]/50">Biarkan kosong jika tidak ingin mengganti gambar poster yang sudah ada.</p>
            </div>

            <!-- Atau Pilih dari Visual Preset -->
            <div>
                <label class="block text-xs font-semibold text-[#212121] mb-1.5">Atau Pilih dari Poster Preset Resmi:</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="cursor-pointer border border-[#212121]/15 rounded-xl p-2 bg-[#F9F8F6] hover:border-[#6D0808]/40 transition block">
                        <input type="radio" name="preset_image" value="images/banners/banner_valorant.jpg" class="sr-only peer" {{ $banner->image === 'images/banners/banner_valorant.jpg' ? 'checked' : '' }}>
                        <div class="peer-checked:ring-2 peer-checked:ring-[#6D0808] rounded-lg overflow-hidden">
                            <img src="{{ asset('images/banners/banner_valorant.jpg') }}" class="w-full h-16 object-cover rounded-lg">
                            <span class="block text-[10px] font-bold text-center text-[#212121] mt-1">Valorant Agents</span>
                        </div>
                    </label>
                    <label class="cursor-pointer border border-[#212121]/15 rounded-xl p-2 bg-[#F9F8F6] hover:border-[#6D0808]/40 transition block">
                        <input type="radio" name="preset_image" value="images/banners/banner_freefire.jpg" class="sr-only peer" {{ $banner->image === 'images/banners/banner_freefire.jpg' ? 'checked' : '' }}>
                        <div class="peer-checked:ring-2 peer-checked:ring-[#6D0808] rounded-lg overflow-hidden">
                            <img src="{{ asset('images/banners/banner_freefire.jpg') }}" class="w-full h-16 object-cover rounded-lg">
                            <span class="block text-[10px] font-bold text-center text-[#212121] mt-1">Free Fire Booyah</span>
                        </div>
                    </label>
                    <label class="cursor-pointer border border-[#212121]/15 rounded-xl p-2 bg-[#F9F8F6] hover:border-[#6D0808]/40 transition block">
                        <input type="radio" name="preset_image" value="images/banners/banner_codm.jpg" class="sr-only peer" {{ $banner->image === 'images/banners/banner_codm.jpg' ? 'checked' : '' }}>
                        <div class="peer-checked:ring-2 peer-checked:ring-[#6D0808] rounded-lg overflow-hidden">
                            <img src="{{ asset('images/banners/banner_codm.jpg') }}" class="w-full h-16 object-cover rounded-lg">
                            <span class="block text-[10px] font-bold text-center text-[#212121] mt-1">Call of Duty Mobile</span>
                        </div>
                    </label>
                    <label class="cursor-pointer border border-[#212121]/15 rounded-xl p-2 bg-[#F9F8F6] hover:border-[#6D0808]/40 transition block">
                        <input type="radio" name="preset_image" value="images/banners/banner_aov.jpg" class="sr-only peer" {{ $banner->image === 'images/banners/banner_aov.jpg' ? 'checked' : '' }}>
                        <div class="peer-checked:ring-2 peer-checked:ring-[#6D0808] rounded-lg overflow-hidden">
                            <img src="{{ asset('images/banners/banner_aov.jpg') }}" class="w-full h-16 object-cover rounded-lg">
                            <span class="block text-[10px] font-bold text-center text-[#212121] mt-1">Arena of Valor</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Teks & Promosi -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-[#212121] uppercase tracking-wider flex items-center space-x-2 pb-3 border-b border-[#212121]/10">
                <x-icon name="tag" class="w-4 h-4 text-amber-600" />
                <span>2. Konten Teks &amp; Penawaran Komersial</span>
            </h2>

            <!-- Badge Text (Pill Atas) -->
            <div>
                <label class="block text-xs font-semibold text-[#212121] mb-1">Badge / Label Atas</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="Contoh: PROMO WEEKEND GAMING KAIDEN" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
            </div>

            <!-- Judul Utama -->
            <div>
                <label class="block text-xs font-semibold text-[#212121] mb-1">Judul Utama Promo <span class="text-red-600">*</span></label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}" required placeholder="Contoh: Beli Diamond & Voucher Pakai QRIS / E-Wallet" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
            </div>

            <!-- Subtitle / Deskripsi Singkat Bawah -->
            <div>
                <label class="block text-xs font-semibold text-[#212121] mb-1">Keterangan / Subjudul Bawah</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="Contoh: Setiap Jumat, Sabtu, Minggu • Otomatis dipotong saat checkout" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
            </div>

            <!-- Highlight Diskon (Tag, Nominal, Syarat) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Tag Diskon (Kiri)</label>
                    <input type="text" name="discount_tag" value="{{ old('discount_tag', $banner->discount_tag) }}" placeholder="Contoh: DISKON! / BONUS! / HEMAT" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Nominal / Nilai</label>
                    <input type="text" name="discount_value" value="{{ old('discount_value', $banner->discount_value) }}" placeholder="Contoh: 20 RB / +15% DM / Rp 0" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Keterangan Tambahan</label>
                    <input type="text" name="discount_subtext" value="{{ old('discount_subtext', $banner->discount_subtext) }}" placeholder="Contoh: Min. Trx 45rb / Kuota Terbatas" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>
            </div>
        </div>

        <!-- Bagian 3: Tombol Aksi, Urutan & Status -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-[#212121] uppercase tracking-wider flex items-center space-x-2 pb-3 border-b border-[#212121]/10">
                <x-icon name="link" class="w-4 h-4 text-emerald-600" />
                <span>3. Tombol Aksi (Call To Action) &amp; Pengaturan</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Teks Tombol <span class="text-red-600">*</span></label>
                    <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" required placeholder="Contoh: Klaim Promo / Top Up Sekarang / Beli Sekarang" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Target URL / Tautan <span class="text-red-600">*</span></label>
                    <input type="text" name="button_url" value="{{ old('button_url', $banner->button_url) }}" required placeholder="Contoh: #katalog-game atau /game/mobile-legends atau https://wa.me/..." class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] font-mono focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#212121] mb-1">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#212121]/15 text-xs text-[#212121] font-mono focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                </div>

                <div class="flex items-center space-x-3 pt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#6D0808] focus:ring-[#6D0808] border-[#212121]/20">
                    <label for="is_active" class="text-xs font-bold text-[#212121] select-none cursor-pointer">
                        Aktifkan Langsung di Beranda
                    </label>
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('admin.banners.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121]/70 hover:text-[#212121] hover:bg-[#212121]/5 transition">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                <x-icon name="check" class="w-4 h-4" />
                <span>Simpan Perubahan Banner</span>
            </button>
        </div>

    </form>
</div>
@endsection
