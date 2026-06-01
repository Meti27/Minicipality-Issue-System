<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $complaintId,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly ?string $comment,
        public readonly string $changedByName,
        public readonly string $timestamp,
        public readonly ?string $rejectionReason,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("complaint.{$this->complaintId}")];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }
}
