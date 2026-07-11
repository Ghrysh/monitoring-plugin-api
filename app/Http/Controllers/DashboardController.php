<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorLog;

class DashboardController extends Controller
{
    private function getClientId(Request $request = null)
    {
        if ($request && $request->has('license')) {
            $client = \App\Models\Client::where('license_key', $request->query('license'))
                ->where('status', 'active')
                ->first();
            if ($client) return $client->id;
        }
        
        $client = \App\Models\Client::first();
        return $client ? $client->id : null;
    }

    public function index(Request $request)
    {
        $clientId = $this->getClientId($request);
        $filter = $request->query('filter', 'today');
        
        $query = VisitorLog::where('client_id', $clientId);
        
        if ($filter == 'today') {
            $query->where('date', now()->toDateString());
        } elseif ($filter == 'month') {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($filter == 'year') {
            $query->whereYear('date', now()->year);
        }

        $visitorLogs = (clone $query)->latest('updated_at')->paginate(20, ['*'], 'journey_page');
        $totalVisitors = (clone $query)->count();

        $chartLabels = [];
        $chartValues = [];

        if ($filter == 'today') {
            $stats = (clone $query)->selectRaw('EXTRACT(HOUR FROM created_at) as hour, count(*) as count')
                ->groupBy('hour')->pluck('count', 'hour')->toArray();
            for ($i = 0; $i < 24; $i++) {
                $chartLabels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $chartValues[] = $stats[$i] ?? 0;
            }
        } elseif ($filter == 'month') {
            $stats = (clone $query)->selectRaw('EXTRACT(DAY FROM created_at) as day, count(*) as count')
                ->groupBy('day')->pluck('count', 'day')->toArray();
            for ($i = 1; $i <= now()->daysInMonth; $i++) {
                $chartLabels[] = (string)$i;
                $chartValues[] = $stats[$i] ?? 0;
            }
        } elseif ($filter == 'year') {
            $stats = (clone $query)->selectRaw('EXTRACT(MONTH FROM created_at) as month, count(*) as count')
                ->groupBy('month')->pluck('count', 'month')->toArray();
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = $months[$i-1];
                $chartValues[] = $stats[$i] ?? 0;
            }
        }

        $chartData = [
            'labels' => $chartLabels,
            'values' => $chartValues,
            'labelName' => 'Total Pengunjung'
        ];

        $isEmbed = $request->is('embed/*');

        return view('dashboard', compact('visitorLogs', 'totalVisitors', 'chartData', 'filter', 'isEmbed'));
    }

    public function embedDashboard(Request $request)
    {
        return $this->index($request);
    }
}
