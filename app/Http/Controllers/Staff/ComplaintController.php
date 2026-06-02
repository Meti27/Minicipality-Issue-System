<?php

namespace App\Http\Controllers\Staff;

use App\Events\ComplaintStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    // Valid transitions: current status → allowed next statuses
    private const TRANSITIONS = [
        'submitted'      => ['pending_review'],
        'pending_review' => ['validated', 'rejected'],
        'validated'      => ['in_progress'],
        'in_progress'    => ['resolved'],
        'resolved'       => ['closed'],
        'closed'         => [],
        'rejected'       => [],
    ];

    public function dashboard(): View
    {
        $stats = [
            'total'          => Complaint::count(),
            'submitted'      => Complaint::where('status', 'submitted')->count(),
            'pending_review' => Complaint::where('status', 'pending_review')->count(),
            'in_progress'    => Complaint::where('status', 'in_progress')->count(),
            'resolved'       => Complaint::where('status', 'resolved')->count(),
            'rejected'       => Complaint::where('status', 'rejected')->count(),
        ];

        $pendingComplaints = Complaint::with(['user', 'category'])
            ->whereIn('status', ['submitted', 'pending_review'])
            ->latest()
            ->take(8)
            ->get();

        return view('staff.dashboard', compact('stats', 'pendingComplaints'));
    }

    public function index(): View
    {
        $status = request('status');
        $search = request('search');

        $complaints = Complaint::with(['user', 'category'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.complaints.index', compact('complaints', 'status', 'search'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['user', 'category', 'statusHistories.changedBy']);

        $allowedTransitions = self::TRANSITIONS[$complaint->status] ?? [];

        // Possible duplicates: other complaints in the same category still active
        $duplicates = Complaint::with('user')
            ->where('category_id', $complaint->category_id)
            ->where('id', '!=', $complaint->id)
            ->whereNotIn('status', ['closed', 'rejected'])
            ->latest()
            ->take(5)
            ->get();

        return view('staff.complaints.show', compact('complaint', 'allowedTransitions', 'duplicates'));
    }

    public function updateStatus(UpdateComplaintStatusRequest $request, Complaint $complaint): RedirectResponse
    {
        $newStatus = $request->validated()['new_status'];
        $allowed   = self::TRANSITIONS[$complaint->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return back()->withErrors(['new_status' => "Cannot transition from '{$complaint->status}' to '{$newStatus}'."]);
        }

        $oldStatus = $complaint->status;

        $complaint->status = $newStatus;

        if ($newStatus === 'rejected') {
            $complaint->rejection_reason = $request->validated()['rejection_reason'];
        }

        $complaint->save();

        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'changed_by'   => auth()->id(),
            'old_status'   => $oldStatus,
            'new_status'   => $newStatus,
            'comment'      => $request->validated()['comment'] ?? null,
        ]);

        Notification::create([
            'user_id'      => $complaint->user_id,
            'complaint_id' => $complaint->id,
            'message'      => "Your complaint \"{$complaint->title}\" has been updated to: " . ucfirst(str_replace('_', ' ', $newStatus)) . '.',
        ]);

        ComplaintStatusUpdated::dispatch(
            $complaint->id,
            $oldStatus,
            $newStatus,
            $request->validated()['comment'] ?? null,
            auth()->user()->name,
            now()->format('d M Y, H:i'),
            $newStatus === 'rejected' ? ($request->validated()['rejection_reason'] ?? null) : null,
        );

        return redirect()->route('staff.complaints.show', $complaint)
            ->with('success', 'Complaint status updated successfully.');
    }
}
