<x-app-layout :isEmbed="$isEmbed ?? false">
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
        .text-blue-custom { color: #2563eb; }
        .bg-blue-light { background-color: #eff6ff; }
        .text-blue-custom { color: #1d4ed8; }
        .bg-blue-light { background-color: #eff6ff; }
    </style>

    <div x-data="{ showModal: false, activeJourney: null }" class="py-8 grid-bg min-h-screen relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-slate-50 inline-flex p-1.5 space-x-1 rounded-xl mb-6">
                <a href="?filter=today" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? 'today') === 'today' ? 'bg-white shadow-sm text-blue-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Hari Ini</a>
                <a href="?filter=month" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? '') === 'month' ? 'bg-white shadow-sm text-blue-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Bulan Ini</a>
                <a href="?filter=year" class="px-5 py-2 rounded-lg text-sm {{ ($filter ?? '') === 'year' ? 'bg-white shadow-sm text-blue-custom font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium' }}">Tahun Ini</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow-sm rounded-xl p-8 flex flex-col items-center justify-center border border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 uppercase tracking-widest mb-3">Total Pengunjung</h3>
                    <p class="text-6xl font-black text-gray-800 mb-4">{{ $totalVisitors ?? 0 }}</p>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-blue-light text-blue-custom">
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
                            <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 whitespace-nowrap text-left text-xs font-bold text-gray-500">IP / Sesi</th>
                                    <th class="px-6 py-4 w-[40%] text-left text-xs font-bold text-gray-500">Alur Singkat</th>
                                    <th class="px-6 py-4 whitespace-nowrap text-left text-xs font-bold text-gray-500">Mulai</th>
                                    <th class="px-6 py-4 whitespace-nowrap text-left text-xs font-bold text-gray-500">Aktivitas Terakhir</th>
                                    <th class="px-6 py-4 text-right whitespace-nowrap text-xs font-bold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($visitorLogs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="font-bold text-slate-900">{{ $log->ip_address }}</div>
                                        <div class="text-xs text-slate-400 truncate w-24" title="{{ $log->session_id }}">ID: {{ substr($log->session_id, 0, 8) }}...</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($log->page_journey && is_array($log->page_journey))
                                                @foreach(array_slice($log->page_journey, 0, 3) as $step)
                                                    <div class="flex items-center gap-1 group relative">
                                                        <span class="px-2 py-1 bg-blue-50 border border-blue-100 text-blue-700 text-[11px] font-medium rounded shadow-sm truncate max-w-[120px]">
                                                            {{ $step['path'] == '/' ? '/ (Home)' : $step['path'] }}
                                                        </span>
                                                        
                                                        @if(!$loop->last || count($log->page_journey) > 3)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                
                                                @if(count($log->page_journey) > 3)
                                                    <span class="text-[11px] text-slate-400 font-bold bg-slate-100 px-2 py-1 rounded">
                                                        +{{ count($log->page_journey) - 3 }} lagi
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic text-xs">Belum ada data alur</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">{{ $log->updated_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($log->page_journey && count($log->page_journey) > 0)
                                            <button @click="activeJourney = {{ json_encode($log->page_journey) }}; showModal = true" class="text-blue-600 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-colors whitespace-nowrap">Lihat Full</button>
                                        @endif
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
                        <div class="relative pl-6 ml-2" x-show="activeJourney && activeJourney.length > 0">
                            <!-- Vertical Line -->
                            <div class="absolute top-0 bottom-0 left-[7px] w-px bg-blue-100"></div>
                            
                            <template x-for="(step, index) in activeJourney" :key="index">
                                <!-- Item -->
                                <div class="relative pb-2">
                                    <!-- Dot -->
                                    <div class="absolute w-[9px] h-[9px] bg-blue-500 rounded-full -left-[23px] top-[18px] ring-4 ring-white"></div>
                                    
                                    <!-- Card -->
                                    <div class="bg-gray-50 rounded-[14px] p-3.5 flex items-center justify-between border border-gray-100">
                                        <div>
                                            <p class="text-[13px] font-bold text-blue-600" x-text="step.path === '/' ? '/ (Home)' : step.path"></p>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider" x-text="'Langkah ke-' + (index + 1)"></p>
                                        </div>
                                        <div class="bg-white px-2 py-1 rounded-md border border-gray-100 text-[11px] font-bold text-gray-600 shadow-sm" x-text="step.time"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="!activeJourney || activeJourney.length === 0" class="text-center py-4 text-sm text-gray-400">
                            Tidak ada data perjalanan untuk sesi ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            const ctx = document.getElementById('visitorChart');
            
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: chartData.labelName,
                            data: chartData.values,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
