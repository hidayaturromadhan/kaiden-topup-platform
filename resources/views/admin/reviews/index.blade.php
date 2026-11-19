@extends('layouts.admin')

@section('title', 'Feedback & Ulasan Pelanggan - KAIDEN')
@section('header_title', 'Kelola Feedback & Pop-up Ulasan Pelanggan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-[#212121]">Feedback &amp; Ulasan Pelanggan</h1>
            <p class="text-xs text-[#212121]/60 mt-1">Kelola rating &amp; ulasan pembeli serta atur ulasan yang tampil pada pop-up transaksi langsung di beranda.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Pop-up Beranda Aktif</span>
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold ml-4">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <x-icon name="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0" />
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold ml-4">✕</button>
        </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Feedback -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Total Feedback</div>
                <div class="text-2xl font-black font-mono text-[#212121] mt-1">{{ $totalCount }}</div>
                <div class="text-[11px] text-[#212121]/50 mt-0.5">Seluruh ulasan pembeli</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#6D0808]/10 text-[#6D0808] border border-[#6D0808]/20 flex items-center justify-center shrink-0">
                <x-icon name="message-square" class="w-6 h-6" />
            </div>
        </div>

        <!-- Card 2: Rata-rata Rating -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Rata-rata Kepuasan</div>
                <div class="flex items-baseline space-x-2 mt-1">
                    <span class="text-2xl font-black font-mono text-amber-600">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs text-[#212121]/50 font-mono">/ 5.0</span>
                </div>
                <div class="flex items-center space-x-0.5 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <x-icon name="star" class="w-3 h-3 {{ $i <= round($avgRating) ? 'text-amber-400' : 'text-stone-200' }}" fill="currentColor" />
                    @endfor
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                <x-icon name="star" class="w-6 h-6" fill="currentColor" />
            </div>
        </div>

        <!-- Card 3: Tampil di Pop-up -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Tampil di Pop-up</div>
                <div class="text-2xl font-black font-mono text-emerald-700 mt-1">{{ $visibleCount }}</div>
                <div class="text-[11px] text-emerald-700/70 mt-0.5">Sedang tayang di beranda</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center shrink-0">
                <x-icon name="eye" class="w-6 h-6" />
            </div>
        </div>

        <!-- Card 4: Disembunyikan (Hide) -->
        <div class="p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-[#212121]/60 uppercase tracking-wider">Disembunyikan (Hide)</div>
                <div class="text-2xl font-black font-mono text-stone-600 mt-1">{{ $hiddenCount }}</div>
                <div class="text-[11px] text-stone-500 mt-0.5">Tidak tampil pada pop-up</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-600 border border-stone-200 flex items-center justify-center shrink-0">
                <x-icon name="eye-off" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Pencarian Kata Kunci -->
            <div class="lg:col-span-2 relative">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari pelanggan, email, kode pesanan, isi ulasan..." class="w-full pl-8 pr-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>

            <!-- Filter Status Pop-up -->
            <div>
                <select name="visibility" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                    <option value="">Status Pop-up: Semua</option>
                    <option value="visible" {{ $visibilityFilter === 'visible' ? 'selected' : '' }}>Tampil di Pop-up</option>
                    <option value="hidden" {{ $visibilityFilter === 'hidden' ? 'selected' : '' }}>Disembunyikan (Hide)</option>
                </select>
            </div>

            <!-- Filter Rating Bintang -->
            <div>
                <select name="rating" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                    <option value="">Rating: Semua Bintang</option>
                    <option value="5" {{ $ratingFilter === '5' ? 'selected' : '' }}>★★★★★ 5 Bintang ({{ $starCounts[5] }})</option>
                    <option value="4" {{ $ratingFilter === '4' ? 'selected' : '' }}>★★★★☆ 4 Bintang ({{ $starCounts[4] }})</option>
                    <option value="3" {{ $ratingFilter === '3' ? 'selected' : '' }}>★★★☆☆ 3 Bintang ({{ $starCounts[3] }})</option>
                    <option value="2" {{ $ratingFilter === '2' ? 'selected' : '' }}>★★☆☆☆ 2 Bintang ({{ $starCounts[2] }})</option>
                    <option value="1" {{ $ratingFilter === '1' ? 'selected' : '' }}>★☆☆☆☆ 1 Bintang ({{ $starCounts[1] }})</option>
                </select>
            </div>

            <!-- Tombol Aksi Filter & Reset -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition text-center">
                    Terapkan
                </button>
                @if($search || $visibilityFilter || $ratingFilter || $gameFilter)
                    <a href="{{ route('admin.reviews.index') }}" class="px-3 py-2 rounded-xl text-xs font-medium text-[#212121]/70 hover:text-[#212121] bg-stone-100 hover:bg-stone-200 transition" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Reviews -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Pelanggan &amp; Pesanan</th>
                        <th class="py-3 px-4">Game &amp; Produk</th>
                        <th class="py-3 px-4">Rating &amp; Ulasan Pembeli</th>
                        <th class="py-3 px-4 text-center">Status Pop-up Beranda</th>
                        <th class="py-3 px-4 text-right">Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <!-- Pelanggan & Pesanan -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-[#6D0808]/10 text-[#6D0808] flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#212121]">{{ $rev->user->name ?? 'Pelanggan' }}</div>
                                        <div class="font-mono text-[11px] text-[#212121]/60">{{ $rev->user->email ?? '-' }}</div>
                                        @if($rev->order)
                                            <div class="mt-1 flex items-center space-x-1.5">
                                                <a href="{{ route('admin.orders.show', $rev->order->id) }}" class="inline-flex items-center space-x-1 font-mono text-[10px] font-semibold text-[#6D0808] hover:underline" title="Lihat detail pesanan">
                                                    <span>{{ $rev->order->order_code }}</span>
                                                    <x-icon name="arrow-right" class="w-2.5 h-2.5" />
                                                </a>
                                                @if($rev->order->confirmed_nickname)
                                                    <span class="text-[10px] text-stone-400">• Nick: <strong class="text-stone-600">{{ $rev->order->confirmed_nickname }}</strong></span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Game & Produk -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-2.5">
                                    @php
                                        $game = $rev->game ?? ($rev->order?->product?->game);
                                        $thumb = $game?->thumbnail ?? 'images/bg/1.jpg';
                                    @endphp
                                    <img src="{{ asset($thumb) }}" alt="{{ $game->name ?? 'Game' }}" class="w-9 h-9 rounded-xl object-cover border border-[#212121]/10 shrink-0">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-[#212121] truncate">{{ $game->name ?? 'Game' }}</div>
                                        <div class="text-[11px] text-[#212121]/60 truncate">{{ $rev->order?->product?->name ?? 'Item Produk' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Rating & Ulasan -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <div class="flex items-center space-x-1 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" class="w-3.5 h-3.5 {{ $i <= $rev->rating ? 'text-amber-400' : 'text-stone-200' }}" fill="currentColor" />
                                    @endfor
                                    <span class="ml-1.5 font-mono text-[11px] font-bold text-amber-700">{{ $rev->rating }}.0</span>
                                    <span class="text-[10px] text-[#212121]/40 ml-2 font-mono">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>

                                @if($rev->comment)
                                    <p class="text-xs text-[#212121]/80 italic line-clamp-2">
                                        “{{ $rev->comment }}”
                                    </p>
                                @else
                                    <span class="text-[11px] text-[#212121]/40 italic">(Hanya memberikan rating bintang)</span>
                                @endif

                                @if($rev->admin_reply)
                                    <div class="mt-2 p-2 rounded-xl bg-rose-50/60 border border-rose-100 text-[11px] text-[#6D0808]">
                                        <span class="font-bold">Balasan Admin:</span> {{ $rev->admin_reply }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status Pop-up Beranda -->
                            <td class="py-3.5 px-4 text-center">
                                @if($rev->is_visible)
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200" title="Ulasan ini aktif berputar pada pop-up transaksi beranda">
                                        <x-icon name="eye" class="w-3.5 h-3.5 text-emerald-600" />
                                        <span>Tampil di Pop-up</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-stone-600 bg-stone-100 border border-stone-200" title="Ulasan ini disembunyikan dan tidak akan muncul di pop-up beranda">
                                        <x-icon name="eye-off" class="w-3.5 h-3.5 text-stone-500" />
                                        <span>Disembunyikan (Hide)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Kelola -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Form Toggle Pop-up Visibility -->
                                    <form action="{{ route('admin.reviews.toggle_popup', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($rev->is_visible)
                                            <button type="submit" class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-stone-700 bg-stone-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 border border-stone-200 transition" title="Sembunyikan dari pop-up beranda">
                                                <x-icon name="eye-off" class="w-3.5 h-3.5" />
                                                <span>Hide Pop-up</span>
                                            </button>
                                        @else
                                            <button type="submit" class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition" title="Tampilkan pada pop-up transaksi beranda">
                                                <x-icon name="eye" class="w-3.5 h-3.5 text-emerald-600" />
                                                <span>Tampilkan</span>
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Tombol Balas Admin (Modal) -->
                                    <button type="button" onclick="openReplyModal({{ $rev->id }}, '{{ addslashes($rev->user->name ?? 'Pelanggan') }}', '{{ addslashes($rev->comment ?? '') }}', '{{ addslashes($rev->admin_reply ?? '') }}')" class="p-1.5 text-stone-500 hover:text-[#6D0808] hover:bg-rose-50 rounded-lg transition" title="Beri atau ubah balasan resmi untuk ulasan ini">
                                        <x-icon name="message-square" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto">
                                        <x-icon name="message-square" class="w-6 h-6" />
                                    </div>
                                    <div class="font-bold text-[#212121]">Belum Ada Feedback Pelanggan</div>
                                    <p class="text-xs text-[#212121]/60">Saat pelanggan menyelesaikan transaksi dan mengirimkan ulasan bintang, catatan ulasan dan kontrol pop-up akan otomatis tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Balas Ulasan Pelanggan -->
<div id="reply-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
    <div class="w-full max-w-md bg-white rounded-2xl border border-[#212121]/15 shadow-xl p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#212121]">Balasan Resmi Admin</h3>
            <button type="button" onclick="closeReplyModal()" class="text-stone-400 hover:text-stone-700">✕</button>
        </div>

        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200/60 text-xs">
            <div class="text-[11px] font-semibold text-stone-500">Ulasan dari <span id="modal-customer-name" class="text-stone-800 font-bold"></span>:</div>
            <p id="modal-customer-comment" class="mt-1 text-stone-700 italic"></p>
        </div>

        <form id="reply-form" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="admin_reply_text" class="block text-xs font-semibold text-[#212121] mb-1.5">Tulis Balasan Admin:</label>
                <textarea name="admin_reply" id="admin_reply_text" rows="3" maxlength="500" placeholder="Terima kasih atas penilaian positif Anda! Kami akan terus meningkatkan kualitas layanan..." class="w-full p-3 rounded-xl border border-[#212121]/15 text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20"></textarea>
                <div class="text-[10px] text-stone-400 mt-1">Balasan ini akan tampil pada riwayat pesanan pelanggan.</div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="closeReplyModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 hover:bg-stone-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                    Simpan Balasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReplyModal(id, customerName, comment, reply) {
        const modal = document.getElementById('reply-modal');
        const form = document.getElementById('reply-form');
        const nameEl = document.getElementById('modal-customer-name');
        const commentEl = document.getElementById('modal-customer-comment');
        const replyText = document.getElementById('admin_reply_text');

        form.action = '/admin/reviews/' + id + '/reply';
        nameEl.textContent = customerName;
        commentEl.textContent = comment ? '“' + comment + '”' : '(Pelanggan hanya memberikan rating tanpa teks)';
        replyText.value = reply || '';

        modal.classList.remove('hidden');
    }

    function closeReplyModal() {
        document.getElementById('reply-modal').classList.add('hidden');
    }
</script>
@endsection
