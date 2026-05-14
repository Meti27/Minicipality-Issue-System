<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userStats = [
            'total'   => User::count(),
            'citizens' => User::where('role', 'citizen')->count(),
            'staff'   => User::where('role', 'staff')->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        $complaintStats = [
            'total'          => Complaint::count(),
            'submitted'      => Complaint::where('status', 'submitted')->count(),
            'pending_review' => Complaint::where('status', 'pending_review')->count(),
            'in_progress'    => Complaint::where('status', 'in_progress')->count(),
            'resolved'       => Complaint::where('status', 'resolved')->count(),
            'rejected'       => Complaint::where('status', 'rejected')->count(),
        ];

        $recentComplaints = Complaint::with(['user', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'userStats', 'complaintStats', 'recentComplaints', 'recentUsers'
        ));
    }
}
