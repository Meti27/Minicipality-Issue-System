<?php

use App\Models\Complaint;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('complaint.{complaintId}', function ($user, $complaintId) {
    $complaint = Complaint::find($complaintId);
    if (!$complaint) {
        return false;
    }
    return $user->id === $complaint->user_id || in_array($user->role, ['staff', 'admin']);
});
