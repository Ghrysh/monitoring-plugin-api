<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'today');
        
        $query = VisitorLog::query();
        
        $labels = [];
        $chartData = [];
        
        if ($filter === 'month') {
            $query->whereMonth('visited_at', now()->month)
                  ->whereYear('visited_at', now()->year);
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = $i;
                $chartData[$i] = 0;
            }
            $periodLabel = 'Bulan ini';
        } elseif ($filter === 'year') {
            $query->whereYear('visited_at', now()->year);
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($months as $idx => $m) {
                $labels[] = $m;
                $chartData[$idx + 1] = 0;
            }
            $periodLabel = 'Tahun ini';
        } else {
            $filter = 'today';
            $query->whereDate('visited_at', today());
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $chartData[$i] = 0;
            }
            $periodLabel = 'Hari ini';
        }

        $visitorLogs = $query->latest('visited_at')->paginate(20);
        $totalVisitors = (clone $query)->count();

        $logs = (clone $query)->get();
        foreach ($logs as $log) {
            $date = \Carbon\Carbon::parse($log->visited_at);
            if ($filter === 'month') {
                $chartData[$date->day]++;
            } elseif ($filter === 'year') {
                $chartData[$date->month]++;
            } else {
                $chartData[$date->hour]++;
            }
        }

        $chartDataValues = array_values($chartData);

        return view('dashboard', compact('visitorLogs', 'totalVisitors', 'chartDataValues', 'labels', 'filter', 'periodLabel'));
    }
}
