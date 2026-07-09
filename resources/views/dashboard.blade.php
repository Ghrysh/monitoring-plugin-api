<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Custom CSS for exact styling -->
    <style>
        .grid-bg {
            background-color: #fcfcfc;
            background-image: linear-gradient(#f0f0f0 1px, transparent 1px), linear-gradient(90deg, #f0f0f0 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .text-teal-custom { color: #14b8a6; }
        .bg-teal-light { background-color: #f0fdfa; }
        .text-indigo-custom { color: #5c6ac4; }
        .bg-indigo-light { background-color: #f0f4ff; }
    </style>

    <div x-data="{ showModal: false, activeLog: null }" class="py-8 grid-bg min-h-screen relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-slate-50 inline-flex p-1.5 space-x-1 rounded-xl mb-6">
                <a href="?filter=today" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? 'today') === 'today' ? 'bg-white shadow-sm text-teal-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Hari Ini</a>
                <a href="?filter=month" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? '') === 'month' ? 'bg-white shadow-sm text-teal-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Bulan Ini</a>
                <a href="?filter=year" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? '') === 'year' ? 'bg-white shadow-sm text-teal-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Tahun Ini</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow-sm rounded-xl p-8 flex flex-col items-center justify-center border border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 uppercase tracking-widest mb-3">Total Pengunjung</h3>
                    <p class="text-6xl font-black text-gray-800 mb-4">{{ $totalVisitors ?? 0 }}</p>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-teal-light text-teal-custom">
                        Sesi Aktif: {{ $periodLabel ?? 'Hari ini' }}
                    </span>
                </div>

                <div class="bg-white shadow-sm rounded-xl p-4 lg:col-span-2 border border-gray-100">
                    <div class="relative h-64 w-full">
                        <canvas id="visitorChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Perjalanan Pengunjung (Visitor Journey)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-500">IP / Sesi</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-500">Alur Singkat</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-500">Mulai</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-500">Aktivitas Terakhir</th>
                                    <th class="px-4 py-4 text-right text-xs font-bold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($visitorLogs as $log)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $log->ip_address }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">ID: {{ Str::limit($log->session_id ?? 'Unknown', 12) }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-bold bg-indigo-light text-indigo-custom">
                                                {{ $log->page_url ?? '/ (Unknown)' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                            {{ \Carbon\Carbon::parse($log->visited_at)->format('H:i:s') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-teal-light text-teal-custom">
                                                {{ \Carbon\Carbon::parse($log->visited_at)->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right">
                                            <button @click="activeLog = { ip: '{{ $log->ip_address }}', url: '{{ $log->page_url }}', time: '{{ \Carbon\Carbon::parse($log->visited_at)->format('H:i') }}' }; showModal = true" class="inline-flex items-center px-4 py-1.5 border border-transparent text-xs font-bold rounded bg-indigo-light text-indigo-custom hover:bg-indigo-100 transition-colors">
                                                Lihat Full
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Belum ada data pengunjung.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($visitorLogs->hasPages())
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-500 font-medium">
                            Showing {{ $visitorLogs->firstItem() }} to {{ $visitorLogs->lastItem() }} of {{ $visitorLogs->total() }} results
                        </div>
                        <div class="flex shadow-sm rounded">
                            <!-- Prev -->
                            @if ($visitorLogs->onFirstPage())
                                <span class="px-3.5 py-1.5 bg-[#2a3042] text-white opacity-50 rounded-l text-sm font-medium cursor-not-allowed">&lt;</span>
                            @else
                                <a href="{{ $visitorLogs->previousPageUrl() }}" class="px-3.5 py-1.5 bg-[#2a3042] text-white rounded-l text-sm font-medium hover:bg-gray-700">&lt;</a>
                            @endif
                            
                            <!-- Numbers -->
                            @foreach ($visitorLogs->getUrlRange(1, $visitorLogs->lastPage()) as $page => $url)
                                @if ($page == $visitorLogs->currentPage())
                                    <span class="px-3.5 py-1.5 bg-[#3b4358] text-white text-sm font-medium">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3.5 py-1.5 bg-[#2a3042] text-white text-sm font-medium hover:bg-gray-700">{{ $page }}</a>
                                @endif
                            @endforeach

                            <!-- Next -->
                            @if ($visitorLogs->hasMorePages())
                                <a href="{{ $visitorLogs->nextPageUrl() }}" class="px-3.5 py-1.5 bg-[#2a3042] text-white rounded-r text-sm font-medium hover:bg-gray-700">&gt;</a>
                            @else
                                <span class="px-3.5 py-1.5 bg-[#2a3042] text-white opacity-50 rounded-r text-sm font-medium cursor-not-allowed">&gt;</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" aria-modal="true" style="display: none;">
            <!-- Background Overlay with Blur -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" 
                 @click="showModal = false" aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95" 
                 class="relative bg-white rounded-[20px] text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-[420px] z-10">
                    
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-[17px] font-bold text-gray-900 tracking-tight">
                                Timeline Pengunjung
                            </h3>
                            <button @click="showModal = false" class="bg-gray-50 hover:bg-gray-100 text-gray-400 rounded-lg p-1.5 transition-colors border border-gray-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Timeline Content -->
                        <div class="relative pl-6 ml-2">
                            <!-- Vertical Line -->
                            <div class="absolute top-0 bottom-0 left-[7px] w-px bg-indigo-100"></div>
                            
                            <!-- Item -->
                            <div class="relative pb-2">
                                <!-- Dot -->
                                <div class="absolute w-[9px] h-[9px] bg-indigo-500 rounded-full -left-[23px] top-[18px] ring-4 ring-white"></div>
                                
                                <!-- Card -->
                                <div class="bg-gray-50 rounded-[14px] p-3.5 flex items-center justify-between border border-gray-100">
                                    <div>
                                        <p class="text-[13px] font-bold text-indigo-600" x-text="activeLog?.url || '/ (Home)'"></p>
                                        <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">Langkah ke-1</p>
                                    </div>
                                    <div class="bg-white px-2 py-1 rounded-md border border-gray-100 text-[11px] font-bold text-gray-600 shadow-sm" x-text="activeLog?.time || '14:22'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('visitorChart').getContext('2d');
            
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(20, 184, 166, 0.2)');
            gradient.addColorStop(1, 'rgba(20, 184, 166, 0)');

            const data = {
                labels: @json($labels ?? []),
                datasets: [{
                    label: 'Visitors',
                    data: @json($chartDataValues ?? []),
                    borderColor: '#14b8a6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#14b8a6',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#2a3042',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            displayColors: true,
                            boxPadding: 4,
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    let prefix = 'Pengunjung per Jam: ';
                                    @if(($filter ?? 'today') === 'month')
                                        prefix = 'Pengunjung per Hari: ';
                                    @elseif(($filter ?? '') === 'year')
                                        prefix = 'Pengunjung per Bulan: ';
                                    @endif
                                    return prefix + context.parsed.y;
                                },
                                labelColor: function(context) {
                                    return {
                                        borderColor: '#14b8a6',
                                        backgroundColor: '#ffffff',
                                        borderWidth: 2,
                                        borderRadius: 0,
                                    };
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: {{ ($filter ?? 'today') === 'month' ? 12 : (($filter ?? '') === 'year' ? 306 : 5) }},
                            grid: { color: '#f0f0f0', drawBorder: false },
                            ticks: { 
                                color: '#9ca3af', 
                                font: { size: 11 }, 
                                precision: 0,
                                stepSize: {{ ($filter ?? 'today') === 'month' ? 2 : (($filter ?? '') === 'year' ? 34 : 1) }}
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#9ca3af', font: { size: 11 }, maxRotation: 0, minRotation: 0 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
