@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-gray-900 font-medium">Dashboard Administrator</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-gray-500 mt-1 text-sm">Ringkasan aktivitas dan performa toko hari ini.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0" x-data="{ showPeriodDropdown: false }">
            <div class="relative">
                <button @click="showPeriodDropdown = !showPeriodDropdown" 
                        class="bg-white border border-gray-200 px-4 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors shadow-sm flex items-center gap-2 font-medium focus:outline-none h-10">
                    <i class="ph ph-calendar w-5 h-5 text-gray-500"></i>
                    <span id="period-label">Bulan Ini</span>
                    <i class="ph ph-caret-down w-3.5 h-3.5 ml-1 text-gray-400"></i>
                </button>
                <div x-show="showPeriodDropdown" 
                     @click.away="showPeriodDropdown = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50 py-2"
                     style="display: none;">
                    <a href="?period=today" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Hari Ini</a>
                    <a href="?period=week" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Minggu Ini</a>
                    <a href="?period=month" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-semibold bg-primary-50">Bulan Ini</a>
                    <a href="?period=year" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Tahun Ini</a>
                    <a href="?period=all" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Semua Periode</a>
                </div>
            </div>
            <a href="/admin/dashboard/export/pdf" class="btn-primary flex items-center gap-2 h-10 px-4 shadow-sm">
                <i class="ph ph-download-simple ph-bold w-4 h-4"></i> 
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards (4 columns) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

        <!-- Total Produk -->
        <div class="card p-5 group cursor-pointer hover:border-primary-300 transition-colors bg-gradient-to-br from-white to-primary-50/20">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-primary-100 to-primary-50 p-2.5 rounded-xl text-primary-600 ring-1 ring-primary-200 group-hover:ring-primary-300 transition-all">
                    <i class="ph ph-package w-6 h-6 ph-fill"></i>
                </div>
                <span class="text-xs font-bold text-green-700 bg-gradient-to-r from-green-50 to-green-100/50 px-2 py-0.5 rounded-full border border-green-200 flex items-center gap-1">
                    <i class="ph ph-trend-up ph-bold"></i> Aktif
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 leading-none mb-1">{{ $totalProducts }}</h3>
            <p class="text-sm text-gray-500 font-medium">Total Produk Aktif</p>
        </div>

        <!-- Total Kategori -->
        <div class="card p-5 group cursor-pointer hover:border-amber-200 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-amber-50 p-2.5 rounded-xl text-amber-600 ring-1 ring-amber-100 group-hover:bg-amber-100 transition-colors">
                    <i class="ph ph-squares-four w-6 h-6 ph-fill"></i>
                </div>
                <span class="text-xs font-bold text-gray-600 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-200">
                    Tersedia
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 leading-none mb-1">{{ $totalCategories }}</h3>
            <p class="text-sm text-gray-500 font-medium">Kategori Tersedia</p>
        </div>

        <!-- Pesanan Masuk -->
        <div class="card p-5 group cursor-pointer hover:border-blue-200 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 p-2.5 rounded-xl text-blue-600 ring-1 ring-blue-100 group-hover:bg-blue-100 transition-colors">
                    <i class="ph ph-shopping-cart-simple w-6 h-6 ph-fill"></i>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200 flex items-center gap-1">
                    <i class="ph ph-calendar ph-bold"></i> Bulan Ini
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 leading-none mb-1">{{ $ordersThisMonth }}</h3>
            <p class="text-sm text-gray-500 font-medium">Pesanan Masuk (Bulan Ini)</p>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-5 border border-primary-500 shadow-md group cursor-pointer relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="ph ph-wallet ph-fill w-24 h-24 text-white transform translate-x-4 -translate-y-4"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 p-2.5 rounded-xl text-white ring-1 ring-white/30">
                        <i class="ph ph-currency-circle-dollar w-6 h-6 ph-fill"></i>
                    </div>
                <span class="text-xs font-bold text-green-900 bg-green-400 px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                    <i class="ph ph-calendar ph-bold"></i> Bulan Ini
                </span>
            </div>
            <h3 class="text-2xl font-bold text-white leading-none mb-1">{{ 'Rp' . number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-sm text-primary-100 font-medium">Total Pendapatan</p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Area Chart (Tingkat Kunjungan & Pesanan) -->
    <div class="card p-6 border-primary-100">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Performa Pesanan & Pengunjung</h3>
                <p class="text-sm text-gray-500">Tingkat kunjungan vs konversi pesanan harian</p>
            </div>
            <button class="text-gray-400 hover:text-gray-900">
                <i class="ph ph-dots-three-outline-vertical ph-fill w-5 h-5"></i>
            </button>
        </div>
        <div id="area-chart" class="w-full h-80"></div>
    </div>

    <!-- Charts Row 2: Bar Chart & Donut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Bar Chart: Pendapatan Bulanan -->
        <div class="card p-6 border-primary-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Pendapatan Bulanan</h3>
                    <p class="text-sm text-gray-500">Bandingkan pendapatan antar bulan</p>
                </div>
            </div>
            <div id="bar-chart" class="w-full h-72"></div>
        </div>

        <!-- Donut Chart: Distribusi Kategori -->
        <div class="card p-6 border-primary-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Penjualan per Kategori</h3>
                    <p class="text-sm text-gray-500">Distribusi kategori produk paling laku</p>
                </div>
            </div>
            <div id="donut-chart" class="w-full h-72 flex justify-center items-center"></div>
        </div>
    </div>

    <!-- Bottom Row: Pesanan Terbaru & Stok Hampir Habis -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Pesanan Terbaru Table (Takes 2 cols) -->
        <div class="xl:col-span-2 card overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-primary-100 flex items-center justify-between bg-gradient-to-r from-primary-50/40 to-primary-50/20">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-receipt w-5 h-5 text-primary-600 ph-fill"></i>
                        Pesanan Masuk Terbaru
                    </h2>
                </div>
                <a href="/admin/pesanan" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors group">
                    Semua Pesanan <i class="ph ph-caret-right ph-bold w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm whitespace-nowrap min-w-[500px]">
                    <thead class="bg-gradient-to-r from-primary-50/50 to-primary-50/20 text-primary-700 text-xs font-bold uppercase tracking-wide border-b-2 border-primary-100">
                        <tr>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-primary-50/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $order->user->name ?? 'Guest' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex py-1 px-3 font-bold rounded-full text-xs bg-{{ $order->getStatusColor() }}-50 text-{{ $order->getStatusColor() }}-700 border border-{{ $order->getStatusColor() }}-200">
                                    {{ $order->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">{{ $order->getFormattedTotal() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <i class="ph ph-receipt w-8 h-8 mb-2 mx-auto text-gray-400"></i>
                                <p>Belum ada pesanan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card p-0 border border-red-200 overflow-hidden flex flex-col">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-2">
                <i class="ph ph-warning-circle w-6 h-6 text-red-600 ph-fill"></i>
                <h3 class="font-bold text-red-800 text-base">Stok Hampir Habis</h3>
            </div>
            <div class="p-5 flex-1 space-y-4 bg-white">
                @forelse($lowStockProducts as $product)
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-3 w-[70%]">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 shrink-0 border border-gray-200 overflow-hidden">
                            @if($product->getFirstImage())
                                <img src="{{ $product->getFirstImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <span class="text-gray-800 font-medium truncate">{{ $product->name }}</span>
                    </div>
                    <span class="bg-red-100 text-red-700 font-bold px-2.5 py-1 rounded-md shrink-0 border border-red-200">{{ $product->stock }} {{ $product->unit }}</span>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    <i class="ph ph-check-circle w-6 h-6 mb-2 mx-auto text-green-500"></i>
                    <p class="text-sm">Semua stok aman</p>
                </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-red-100 bg-gradient-to-r from-red-50/30 to-amber-50/10">
                <a href="/admin/stok" class="btn-secondary w-full justify-center shadow-sm border-red-200 hover:bg-red-50 hover:text-red-700">
                    Kelola Stok Masuk
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ApexCharts Script Instantiation -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Common Chart Options for styling matching Tailwind & Flowbite
        const fontFamily = 'Plus Jakarta Sans, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        
        // 1. AREA CHART: Tingkat Kunjungan & Pesanan
        const areaOptions = {
            series: [{
                name: 'Kunjungan Toko',
                data: @json($chartData['visits'])
            }, {
                name: 'Pesanan Dibuat',
                data: @json($chartData['orders'])
            }],
            chart: {
                height: '100%',
                type: 'area',
                fontFamily: fontFamily,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#3b82f6', '#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: @json($chartData['days']),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#6b7280', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#6b7280', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        const areaChart = new ApexCharts(document.querySelector("#area-chart"), areaOptions);
        areaChart.render();


        // 2. BAR CHART: Pendapatan Bulanan
        const barOptions = {
            series: [{
                name: 'Pendapatan',
                data: @json($monthlyRevenue['revenue'])
            }],
            chart: {
                type: 'bar',
                height: '100%',
                fontFamily: fontFamily,
                toolbar: { show: false }
            },
            colors: ['#10b981'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%',
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: @json($monthlyRevenue['months']),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#6b7280', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "Rp" + val + "Jt";
                    },
                    style: { colors: '#6b7280', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
            }
        };

        const barChart = new ApexCharts(document.querySelector("#bar-chart"), barOptions);
        barChart.render();


        // 3. DONUT CHART: Distribusi Penjualan Kategori
        const donutOptions = {
            series: @json($categoryDistribution['data']),
            labels: @json($categoryDistribution['labels']),
            chart: {
                type: 'donut',
                height: '100%',
                fontFamily: fontFamily,
            },
            colors: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { fontSize: '14px', fontFamily: fontFamily, color: '#6b7280' },
                            value: {
                                fontSize: '24px',
                                fontFamily: fontFamily,
                                fontWeight: 700,
                                color: '#111827',
                                formatter: function (val) {
                                    return val + "%"
                                }
                            },
                            total: {
                                show: true,
                                label: 'Produk Utama',
                                formatter: function (w) {
                                    return w.globals.seriesTotals[0] + "%"
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                markers: { radius: 12 }
            },
            stroke: { show: false }
        };

        const donutChart = new ApexCharts(document.querySelector("#donut-chart"), donutOptions);
        donutChart.render();
    });
</script>
@endsection
