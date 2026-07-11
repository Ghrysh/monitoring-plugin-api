<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $visitorLogs = VisitorLog::latest('updated_at')->paginate(20);
        $totalVisitors = VisitorLog::count();
        $todayVisitors = VisitorLog::whereDate('date', today())->count();

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
