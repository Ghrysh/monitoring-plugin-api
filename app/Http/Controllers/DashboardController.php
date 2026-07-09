<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $visitorLogs = VisitorLog::latest('visited_at')->paginate(20);
        $totalVisitors = VisitorLog::count();
        $todayVisitors = VisitorLog::whereDate('visited_at', today())->count();

        return view('dashboard', compact('visitorLogs', 'totalVisitors', 'todayVisitors'));
    }
}
