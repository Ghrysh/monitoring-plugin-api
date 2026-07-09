<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total Pengunjung</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalVisitors) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Pengunjung Hari Ini</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($todayVisitors) }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Log Pengunjung (Visitor Journey)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Halaman</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perangkat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($visitorLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <div>ID Sesi: <span class="font-mono">{{ substr($log->session_id, 0, 8) }}</span></div>
                                            <div class="text-xs">{{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($log->page_journey && is_array($log->page_journey))
                                                <ul class="list-disc list-inside">
                                                @foreach($log->page_journey as $journey)
                                                    <li><span class="font-medium text-indigo-600">{{ $journey['path'] ?? '-' }}</span> <span class="text-xs text-gray-400">({{ $journey['time'] ?? '' }})</span></li>
                                                @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->device ?? 'Unknown' }} - {{ $log->browser ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->city ?? '-' }}, {{ $log->country ?? '-' }}<br>
                                            <span class="text-xs text-gray-400">IP: {{ $log->ip_address }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            Belum ada data pengunjung.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $visitorLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
