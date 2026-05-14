<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()
            ->with('complaint')
            ->latest()
            ->paginate(20);

        // Mark all unread as read on page visit
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return view('citizen.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update(['is_read' => true]);

        if ($notification->complaint_id) {
            return redirect()->route('citizen.complaints.show', $notification->complaint_id);
        }

        return redirect()->route('citizen.notifications.index');
    }

    public function markAllRead(): RedirectResponse
    {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back();
    }
}
