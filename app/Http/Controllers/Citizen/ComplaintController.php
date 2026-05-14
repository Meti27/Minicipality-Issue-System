<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        $stats = [
            'total'      => $user->complaints()->count(),
            'submitted'  => $user->complaints()->where('status', 'submitted')->count(),
            'in_progress'=> $user->complaints()->where('status', 'in_progress')->count(),
            'resolved'   => $user->complaints()->where('status', 'resolved')->count(),
            'rejected'   => $user->complaints()->where('status', 'rejected')->count(),
        ];

        $recentComplaints = $user->complaints()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('citizen.dashboard', compact('stats', 'recentComplaints'));
    }

    public function index(): View
    {
        $complaints = auth()->user()->complaints()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('citizen.complaints.index', compact('complaints'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('citizen.complaints.create', compact('categories'));
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('complaints', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'submitted';

        $complaint = Complaint::create($data);

        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'changed_by'   => auth()->id(),
            'old_status'   => null,
            'new_status'   => 'submitted',
            'comment'      => 'Complaint submitted by citizen.',
        ]);

        return redirect()->route('citizen.complaints.show', $complaint)
            ->with('success', 'Your complaint has been submitted successfully.');
    }

    public function show(Complaint $complaint): View
    {
        // Citizens may only view their own complaints
        abort_unless($complaint->user_id === auth()->id(), 403);

        $complaint->load(['category', 'statusHistories.changedBy']);

        return view('citizen.complaints.show', compact('complaint'));
    }
}
