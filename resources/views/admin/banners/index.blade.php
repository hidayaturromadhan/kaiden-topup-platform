@extends('layouts.admin')

@section('title', 'Kelola Banner & Promo Beranda - KAIDEN')
@section('header_title', 'Kelola Banner & Promo Beranda')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-[#212121]">Kelola Banner &amp; Promo</h1>
            <p class="text-xs text-[#212121]/60 mt-1">Atur poster promosi, diskon khusus, dan banner beranda tanpa tampilan AI.</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs shrink-0">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Banner Baru</span>
        </a>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stat 1: Slider Utama -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Slider Utama (8 Kolom)</div>
                <div class="text-2xl font-black font-mono text-[#212121] mt-1">{{ $totalSlider }}</div>
                <div class="text-[11px] text-[#212121]/50 mt-0.5">Banner carousel besar di beranda</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#6D0808] border border-rose-100 flex items-center justify-center shrink-0">
                <x-icon name="layers" class="w-6 h-6" />
            </div>
        </div>

        <!-- Stat 2: Side Banner -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Side Banner (4 Kolom)</div>
                <div class="text-2xl font-black font-mono text-[#212121] mt-1">{{ $totalSide }}</div>
                <div class="text-[11px] text-[#212121]/50 mt-0.5">Banner penawaran samping</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 border border-amber-100 flex items-center justify-center shrink-0">
                <x-icon name="credit-card" class="w-6 h-6" />
            </div>
        </div>

        <!-- Stat 3: Banner Aktif -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Banner Aktif Tayang</div>
                <div class="text-2xl font-black font-mono text-emerald-700 mt-1">{{ $totalActive }}</div>
                <div class="text-[11px] text-emerald-700/70 mt-0.5">Sedang tampil kepada pengunjung</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center shrink-0">
                <x-icon name="check-circle" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Filter Tab Bar -->
    <div class="flex items-center space-x-2 border-b border-[#212121]/10 pb-3">
        <a href="{{ route('admin.banners.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ !$type ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] hover:bg-[#212121]/5' }}">
            Semua Banner ({{ $banners->total() }})
        </a>
        <a href="{{ route('admin.banners.index', ['type' => 'slider']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $type === 'slider' ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] hover:bg-[#212121]/5' }}">
            Slider Utama ({{ $totalSlider }})
        </a>
        <a href="{{ route('admin.banners.index', ['type' => 'side']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $type === 'side' ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] hover:bg-[#212121]/5' }}">
            Side Banner ({{ $totalSide }})
        </a>
    </div>

    <!-- Banner Table Container -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        @if($banners->isEmpty())
            <div class="p-12 text-center text-[#212121]/60 space-y-3">
                <x-icon name="image" class="w-10 h-10 mx-auto text-[#212121]/30" />
                <h3 class="text-sm font-bold text-[#212121]">Belum Ada Banner Promo</h3>
                <p class="text-xs text-[#212121]/60">Tambahkan banner baru untuk mulai menampilkan promosi menarik di beranda.</p>
                <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Tambah Banner Sekarang</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9F8F6] text-[#212121]/70 uppercase tracking-wider text-[11px] font-bold border-b border-[#212121]/10">
                        <tr>
                            <th class="py-3.5 px-4">Urutan &amp; Tipe</th>
                            <th class="py-3.5 px-4">Visual Poster</th>
                            <th class="py-3.5 px-4">Konten Promo</th>
                            <th class="py-3.5 px-4">Diskon / Tag</th>
                            <th class="py-3.5 px-4">Aksi Link</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#212121]/5 text-[#212121]">
                        @foreach($banners as $banner)
                            <tr class="hover:bg-[#212121]/5 transition">
                                <!-- Urutan & Tipe -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-6 h-6 rounded-md bg-[#212121]/5 text-[#212121] font-mono font-bold text-center flex items-center justify-center text-[11px] border border-[#212121]/10">
                                            {{ $banner->sort_order }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $banner->banner_type === 'slider' ? 'bg-rose-100 text-[#6D0808] border border-rose-200' : 'bg-amber-100 text-amber-900 border border-amber-200' }}">
                                            {{ $banner->banner_type === 'slider' ? 'Slider' : 'Side' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Visual Poster -->
                                <td class="py-3.5 px-4">
                                    <div class="w-20 h-12 rounded-lg overflow-hidden bg-stone-100 border border-[#212121]/15 relative shrink-0 shadow-2xs">
                                        @if($banner->image && file_exists(public_path($banner->image)))
                                            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[#212121]/40 text-[9px] font-medium">
                                                <span>Gradient</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 inset-x-0 h-1 {{ match($banner->gradient_theme) { 'slate' => 'bg-slate-600', 'emerald' => 'bg-emerald-600', 'amber' => 'bg-amber-600', 'blue' => 'bg-blue-600', default => 'bg-[#6D0808]' } }}"></div>
                                    </div>
                                </td>

                                <!-- Konten Promo -->
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="space-y-0.5">
                                        @if($banner->badge_text)
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider bg-[#212121]/5 text-[#212121]/70 border border-[#212121]/10">
                                                {{ $banner->badge_text }}
                                            </span>
                                        @endif
                                        <div class="font-bold text-[#212121] leading-snug line-clamp-1">{{ $banner->title }}</div>
                                        @if($banner->subtitle)
                                            <div class="text-[11px] text-[#212121]/60 line-clamp-1">{{ $banner->subtitle }}</div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Diskon / Tag -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($banner->discount_value)
                                        <div class="space-y-0.5">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1 py-0.2 rounded border border-amber-200 uppercase">{{ $banner->discount_tag ?? 'DISKON' }}</span>
                                                <span class="font-mono font-black text-xs text-[#212121]">{{ $banner->discount_value }}</span>
                                            </div>
                                            @if($banner->discount_subtext)
                                                <div class="text-[10px] text-[#212121]/50 font-mono">{{ $banner->discount_subtext }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[#212121]/30 text-[10px] font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Aksi Link -->
                                <td class="py-3.5 px-4 max-w-[150px]">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-[#212121] text-[11px] truncate">{{ $banner->button_text }}</div>
                                        <div class="text-[10px] text-[#212121]/50 font-mono truncate" title="{{ $banner->button_url }}">{{ $banner->button_url }}</div>
                                    </div>
                                </td>

                                <!-- Status Aktif -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition shadow-2xs {{ $banner->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-800 border border-red-200 hover:bg-red-100' }}" title="Klik untuk beralih status">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $banner->is_active ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                            <span>{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Tombol Kelola -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="p-1.5 rounded-lg bg-[#212121]/5 hover:bg-[#212121]/10 text-[#212121] border border-[#212121]/10 transition" title="Edit Banner">
                                            <x-icon name="edit" class="w-3.5 h-3.5" />
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteBanner(this)" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 transition cursor-pointer" title="Hapus Banner">
                                                <x-icon name="trash" class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($banners->hasPages())
                <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
                    {{ $banners->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function confirmDeleteBanner(button) {
    const form = button.closest('form');
    if (window.kaidenAlert) {
        window.kaidenAlert.confirm(
            'Hapus Banner',
            'Apakah Anda yakin ingin menghapus banner ini secara permanen dari database?',
            function() {
                if (window.kaidenLoading) window.kaidenLoading.show('Menghapus banner...');
                form.submit();
            },
            null,
            'Ya, Hapus',
            'Batal'
        );
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus banner ini?')) {
            form.submit();
        }
    }
}
</script>
@endpush
