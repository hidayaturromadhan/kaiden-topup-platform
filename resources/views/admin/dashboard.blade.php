@extends('layouts.admin')

@section('title', 'Dashboard Operasional & Analitik - KAIDEN')
@section('header_title', 'Ringkasan Operasional & Keuangan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Saldo Deposit Digiflazz Alert / Card (RULES.md Section 5.C.4) -->
    @if($digiflazzBalance)
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start space-x-3 sm:space-x-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $digiflazzBalance['is_low'] ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} flex items-center justify-center shrink-0">
                    <x-icon name="wallet" class="w-5 h-5 sm:w-6 sm:h-6" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span class="text-xs font-semibold text-[#212121]/60 uppercase tracking-wider">Saldo Deposit Digiflazz</span>
                        @if($digiflazzBalance['is_low'])
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-300 animate-pulse">
                                <x-icon name="alert-triangle" class="w-3 h-3 text-red-700" />
                                <span>SALDO KRITIS DI BAWAH AMBANG</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                Saldo Aman
                            </span>
                        @endif
                    </div>
                    <div class="font-mono tabular-nums text-xl sm:text-2xl font-bold text-[#212121] mt-1">
                        Rp {{ number_format($digiflazzBalance['deposit'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#212121]/50 mt-0.5">
                        Ambang Batas Minimum: <span class="font-mono font-semibold">Rp {{ number_format($digiflazzBalance['threshold'], 0, ',', '.') }}</span>
                        @if(!empty($digiflazzBalance['is_mock']))
                            <span class="ml-2 text-amber-700">(Mode Simulasi Kunci Tes)</span>
                        @endif
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasPermission('produk.sinkron'))
                <form action="{{ route('admin.products.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-[#F9F8F6] border border-[#212121]/15 hover:bg-[#212121]/5 transition shadow-xs text-center">
                        <x-icon name="refresh" class="w-4 h-4 text-[#6D0808]" />
                        <span>Sinkron Katalog Digiflazz</span>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Financial KPI Cards: Omset vs Modal vs Keuntungan vs Efisiensi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- 1. Total Omset (Gross Revenue) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#212121]/60">Total Omset</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#6D0808]/10 text-[#6D0808]">Bulan Ini</span>
            </div>
            <div>
                <div class="font-mono tabular-nums text-2xl font-bold text-[#6D0808]">
                    Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#212121]/60 mt-1 flex items-center justify-between border-t border-[#212121]/5 pt-2">
                    <span>Hari Ini: <strong class="font-mono text-[#212121]">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</strong></span>
                    <span class="text-[10px] text-[#212121]/40">All: Rp {{ number_format($allTimeRevenue, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="text-[11px] text-[#212121]/50 flex items-center space-x-1.5">
                <x-icon name="credit-card" class="w-3.5 h-3.5 text-[#6D0808]" />
                <span>{{ $monthOrdersCount }} transaksi berbayar sah</span>
            </div>
        </div>

        <!-- 2. Pengeluaran Modal (Cost / HPP Digiflazz) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#212121]/60">Modal Dikeluarkan</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#212121]/10 text-[#212121]">HPP Digiflazz</span>
            </div>
            <div>
                <div class="font-mono tabular-nums text-2xl font-bold text-[#212121]">
                    Rp {{ number_format($monthCost, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#212121]/60 mt-1 flex items-center justify-between border-t border-[#212121]/5 pt-2">
                    <span>Hari Ini: <strong class="font-mono text-[#212121]">Rp {{ number_format($todayCost, 0, ',', '.') }}</strong></span>
                    <span class="text-[10px] text-[#212121]/40">All: Rp {{ number_format($allTimeCost, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="text-[11px] text-[#212121]/50 flex items-center space-x-1.5">
                <x-icon name="shopping-bag" class="w-3.5 h-3.5 text-[#212121]" />
                <span>Biaya modal terpotong deposit</span>
            </div>
        </div>

        <!-- 3. Keuntungan Bersih (Gross Profit) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-emerald-500/20 shadow-xs flex flex-col justify-between space-y-3 bg-gradient-to-br from-white to-emerald-50/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-800">Keuntungan Bersih</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    +{{ $monthMarginPercent }}% Margin
                </span>
            </div>
            <div>
                <div class="font-mono tabular-nums text-2xl font-bold text-emerald-700">
                    Rp {{ number_format($monthProfit, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-emerald-900/70 mt-1 flex items-center justify-between border-t border-emerald-500/10 pt-2">
                    <span>Hari Ini: <strong class="font-mono text-emerald-800">Rp {{ number_format($todayProfit, 0, ',', '.') }}</strong></span>
                    <span class="text-[10px] text-emerald-800/50">All: Rp {{ number_format($allTimeProfit, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="text-[11px] text-emerald-700/80 flex items-center space-x-1.5">
                <x-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Selisih riil Omset dikurangi HPP</span>
            </div>
        </div>

        <!-- 4. Rata-rata Nilai Pesanan & Konversi -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#212121]/60">Rata-rata Order (AOV)</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    {{ $successRate }}% Sukses
                </span>
            </div>
            <div>
                <div class="font-mono tabular-nums text-2xl font-bold text-[#212121]">
                    Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#212121]/60 mt-1 flex items-center justify-between border-t border-[#212121]/5 pt-2">
                    <span>Sukses: <strong class="font-mono text-emerald-700">{{ $successCount }}</strong></span>
                    <span>Proses: <strong class="font-mono text-amber-700">{{ $pendingCount }}</strong></span>
                    <span>Gagal: <strong class="font-mono text-red-700">{{ $failedCount }}</strong></span>
                </div>
            </div>
            <div class="text-[11px] text-[#212121]/50 flex items-center space-x-1.5">
                <x-icon name="activity" class="w-3.5 h-3.5 text-[#212121]" />
                <span>Dari total {{ $totalOrdersCount }} transaksi tercatat</span>
            </div>
        </div>

    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Main Chart: Tren Finansial Harian (8 Cols on Desktop) -->
        <div class="lg:col-span-8 rounded-2xl bg-white border border-[#212121]/10 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <!-- Header Grafik & Switcher Periode -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#212121]/10">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-bold text-[#212121]">Tren Finansial Harian</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                                Realtime Analytics
                            </span>
                        </div>
                        <p class="text-xs text-[#212121]/60 mt-0.5">Grafik pergerakan Omset Masuk, Modal Digiflazz, dan Keuntungan Bersih.</p>
                    </div>

                    <!-- Range Toggle Buttons -->
                    <div class="inline-flex rounded-xl p-1 bg-[#F9F8F6] border border-[#212121]/10 self-start sm:self-auto text-xs font-semibold">
                        <button type="button" onclick="switchTrendRange('7d')" id="btn-range-7d" class="px-3 py-1 rounded-lg transition text-[#212121]/70 hover:text-[#212121]">
                            7 Hari
                        </button>
                        <button type="button" onclick="switchTrendRange('14d')" id="btn-range-14d" class="px-3 py-1 rounded-lg transition bg-[#6D0808] text-white shadow-xs">
                            14 Hari
                        </button>
                        <button type="button" onclick="switchTrendRange('30d')" id="btn-range-30d" class="px-3 py-1 rounded-lg transition text-[#212121]/70 hover:text-[#212121]">
                            30 Hari
                        </button>
                    </div>
                </div>

                <!-- Quick Summary Stats Strip for Selected Period -->
                <div class="grid grid-cols-3 gap-2 sm:gap-4 my-4 p-3 sm:p-4 rounded-xl bg-[#F9F8F6] border border-[#212121]/5">
                    <div>
                        <div class="text-[11px] font-medium text-[#212121]/60">Omset Periode</div>
                        <div id="period-revenue" class="font-mono text-xs sm:text-sm font-bold text-[#6D0808] mt-0.5">Rp 0</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-medium text-[#212121]/60">Modal Dikeluarkan</div>
                        <div id="period-cost" class="font-mono text-xs sm:text-sm font-bold text-[#212121] mt-0.5">Rp 0</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-medium text-[#212121]/60">Keuntungan Bersih</div>
                        <div id="period-profit" class="font-mono text-xs sm:text-sm font-bold text-emerald-700 mt-0.5">Rp 0</div>
                    </div>
                </div>

                <!-- Chart Canvas Container -->
                <div class="relative w-full h-64 sm:h-72">
                    <canvas id="financialTrendChart"></canvas>
                </div>
            </div>

            <!-- Chart Legend Guide -->
            <div class="mt-4 pt-3 border-t border-[#212121]/5 flex flex-wrap items-center justify-between gap-3 text-xs text-[#212121]/60">
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center space-x-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#6D0808]"></span>
                        <span class="font-medium text-[#212121]">Omset (Jual)</span>
                    </span>
                    <span class="inline-flex items-center space-x-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#64748B]"></span>
                        <span class="font-medium text-[#212121]">Modal (Digiflazz)</span>
                    </span>
                    <span class="inline-flex items-center space-x-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                        <span class="font-medium text-[#212121]">Keuntungan (Laba)</span>
                    </span>
                </div>
                <div class="text-[11px] text-[#212121]/40">
                    Klik label legenda untuk menyembunyikan / menampilkan kurva
                </div>
            </div>
        </div>

        <!-- Side Chart: Performa Kontribusi per Game (4 Cols on Desktop) -->
        <div class="lg:col-span-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-[#212121]/10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#212121]">Kontribusi per Game</h3>
                        <span class="text-[10px] font-semibold text-[#6D0808] uppercase tracking-wider">Top Games</span>
                    </div>
                    <p class="text-xs text-[#212121]/60 mt-0.5">Porsi omset dan keuntungan berdasarkan game.</p>
                </div>

                <!-- Doughnut Chart Canvas Container -->
                <div class="relative w-full h-48 my-3 flex items-center justify-center">
                    <canvas id="gameContributionChart"></canvas>
                </div>

                <!-- Top Games List -->
                <div class="space-y-2.5 mt-2">
                    @forelse($topGames as $index => $game)
                        <div class="p-2.5 rounded-xl bg-[#F9F8F6] border border-[#212121]/5 hover:border-[#6D0808]/20 transition">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2 truncate">
                                    <span class="w-5 h-5 rounded-full bg-[#212121] text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-bold text-[#212121] truncate">{{ $game->game_name }}</span>
                                </div>
                                <span class="font-mono text-xs font-bold text-[#6D0808] shrink-0">
                                    Rp {{ number_format($game->total_revenue, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-[#212121]/60 mt-1 pl-7">
                                <span>Modal: <strong class="font-mono text-[#212121]/80">Rp {{ number_format($game->total_cost, 0, ',', '.') }}</strong></span>
                                <span class="text-emerald-700 font-semibold">
                                    Untung: +Rp {{ number_format($game->profit, 0, ',', '.') }} ({{ $game->margin_percent }}%)
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-[#212121]/50">
                            Belum ada riwayat transaksi per game.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-[#212121]/5 text-[11px] text-[#212121]/50 flex items-center justify-between mt-3">
                <span>Total Game Aktif: <strong>{{ $topGames->count() }}</strong></span>
                <a href="{{ route('admin.reports.index') }}" class="text-[#6D0808] font-semibold hover:underline flex items-center space-x-0.5">
                    <span>Lihat Laporan Lengkap</span>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#212121]/10 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#212121]">Transaksi Terbaru</h3>
                <p class="text-xs text-[#212121]/60">Aktivitas pemesanan teraktual yang masuk ke dalam sistem.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-[#6D0808] hover:underline flex items-center space-x-1">
                <span>Lihat Semua</span>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-8 text-center text-xs text-[#212121]/50">
                Belum ada transaksi yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Game & Produk</th>
                            <th class="py-3 px-4">Nominal</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#212121]/5">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-[#F9F8F6]/60 transition">
                                <td class="py-3 px-4">
                                    <span class="font-mono tabular-nums font-semibold text-[#212121]">{{ $order->order_code }}</span>
                                    <div class="text-[10px] text-[#212121]/50">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-[#212121]">{{ $order->user->name ?? 'Guest' }}</div>
                                    <div class="text-[11px] text-[#212121]/60 font-mono">{{ $order->user_id_game }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-[#212121]">{{ $order->product->game->name }}</div>
                                    <div class="text-[11px] text-[#6D0808]">{{ $order->product->name }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-[#212121]">
                                    Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $badgeColor = match($order->status) {
                                            'SUKSES' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'MENUNGGU_BAYAR' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'DIPROSES' => 'bg-blue-100 text-blue-800 border-blue-300',
                                            default => 'bg-red-100 text-red-800 border-red-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-[#212121] bg-[#F9F8F6] border border-[#212121]/15 hover:bg-[#6D0808] hover:text-white transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Data Tren yang dikirim dari Backend
    const trendDatasets = @json($trends);
    const topGamesData = @json($topGames);

    let financialChart = null;
    let gameChart = null;
    let currentRange = '14d';

    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    // Inisialisasi Grafik Tren Finansial
    function initFinancialChart(rangeKey) {
        const data = trendDatasets[rangeKey];
        if (!data) return;

        // Update Quick Summary Strip
        const totalRev = data.revenue.reduce((a, b) => a + b, 0);
        const totalCost = data.cost.reduce((a, b) => a + b, 0);
        const totalProf = data.profit.reduce((a, b) => a + b, 0);

        document.getElementById('period-revenue').textContent = formatRupiah(totalRev);
        document.getElementById('period-cost').textContent = formatRupiah(totalCost);
        document.getElementById('period-profit').textContent = formatRupiah(totalProf);

        const ctx = document.getElementById('financialTrendChart').getContext('2d');

        if (financialChart) {
            financialChart.destroy();
        }

        financialChart = new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Omset (Jual)',
                        data: data.revenue,
                        borderColor: '#6D0808',
                        backgroundColor: 'rgba(109, 8, 8, 0.05)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#6D0808',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointRadius: 3.5,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Modal (Digiflazz)',
                        data: data.cost,
                        borderColor: '#64748B',
                        backgroundColor: 'rgba(100, 116, 139, 0.05)',
                        borderWidth: 2,
                        pointBackgroundColor: '#64748B',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointRadius: 3,
                        pointHoverRadius: 5.5,
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Keuntungan (Laba)',
                        data: data.profit,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.12)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointRadius: 3.5,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#212121',
                        titleColor: '#F9F8F6',
                        bodyColor: '#F9F8F6',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${formatRupiah(context.raw)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11
                            },
                            color: '#212121'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(33, 33, 33, 0.05)'
                        },
                        ticks: {
                            font: {
                                family: 'JetBrains Mono',
                                size: 11
                            },
                            color: '#212121',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Switcher Periode Tren (7 Hari / 14 Hari / 30 Hari)
    window.switchTrendRange = function(rangeKey) {
        currentRange = rangeKey;

        ['7d', '14d', '30d'].forEach(key => {
            const btn = document.getElementById(`btn-range-${key}`);
            if (key === rangeKey) {
                btn.className = 'px-3 py-1 rounded-lg transition bg-[#6D0808] text-white shadow-xs';
            } else {
                btn.className = 'px-3 py-1 rounded-lg transition text-[#212121]/70 hover:text-[#212121]';
            }
        });

        initFinancialChart(rangeKey);
    };

    // Inisialisasi Grafik Doughnut Kontribusi Game
    function initGameChart() {
        const ctx = document.getElementById('gameContributionChart');
        if (!ctx) return;

        const labels = topGamesData.map(g => g.game_name);
        const revenues = topGamesData.map(g => g.total_revenue);
        const colors = [
            '#6D0808',
            '#212121',
            '#059669',
            '#D97706',
            '#4F46E5',
            '#0284C7'
        ];

        if (labels.length === 0) {
            labels.push('Belum Ada Transaksi');
            revenues.push(1);
            colors.length = 0;
            colors.push('#E2E8F0');
        }

        gameChart = new window.Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: revenues,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#212121',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                if (topGamesData.length === 0) return ' Tidak ada data';
                                return ` ${context.label}: ${formatRupiah(context.raw)}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Pastikan Chart.js sudah siap dari app.js Vite
    function checkAndInitCharts() {
        if (typeof window.Chart !== 'undefined') {
            initFinancialChart(currentRange);
            initGameChart();
        } else {
            setTimeout(checkAndInitCharts, 100);
        }
    }

    document.addEventListener('DOMContentLoaded', checkAndInitCharts);
</script>
@endpush
