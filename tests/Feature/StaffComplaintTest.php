<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffComplaintTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $citizen;
    private Category $category;
    private Complaint $complaint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff    = User::factory()->create(['role' => 'staff']);
        $this->citizen  = User::factory()->create(['role' => 'citizen']);
        $this->category = Category::factory()->create();
        $this->complaint = Complaint::factory()->create([
            'user_id'     => $this->citizen->id,
            'category_id' => $this->category->id,
            'status'      => 'submitted',
        ]);
    }

    // ── Dashboard & Index ─────────────────────────────────────────────────

    /** @test */
    public function staff_dashboard_shows_complaint_counts(): void
    {
        $this->actingAs($this->staff)->get('/staff/dashboard')->assertOk();
    }

    /** @test */
    public function staff_can_list_all_complaints(): void
    {
        $this->actingAs($this->staff)->get('/staff/complaints')->assertOk();
    }

    /** @test */
    public function staff_can_filter_complaints_by_status(): void
    {
        $this->actingAs($this->staff)
            ->get('/staff/complaints?status=submitted')
            ->assertOk();
    }

    /** @test */
    public function staff_can_search_complaints(): void
    {
        $this->actingAs($this->staff)
            ->get('/staff/complaints?search=pothole')
            ->assertOk();
    }

    /** @test */
    public function staff_can_view_a_complaint_detail(): void
    {
        $this->actingAs($this->staff)
            ->get("/staff/complaints/{$this->complaint->id}")
            ->assertOk();
    }

    // ── Valid status transitions ──────────────────────────────────────────

    /** @test */
    public function staff_can_move_submitted_to_pending_review(): void
    {
        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'pending_review',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('complaints', [
            'id'     => $this->complaint->id,
            'status' => 'pending_review',
        ]);
    }

    /** @test */
    public function status_change_creates_history_record(): void
    {
        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'pending_review',
                'comment'    => 'Reviewing now',
            ]);

        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $this->complaint->id,
            'old_status'   => 'submitted',
            'new_status'   => 'pending_review',
            'comment'      => 'Reviewing now',
            'changed_by'   => $this->staff->id,
        ]);
    }

    /** @test */
    public function status_change_creates_notification_for_citizen(): void
    {
        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'pending_review',
            ]);

        $this->assertDatabaseHas('notifications', [
            'user_id'      => $this->citizen->id,
            'complaint_id' => $this->complaint->id,
        ]);
    }

    /** @test */
    public function staff_can_reject_with_reason(): void
    {
        $this->complaint->update(['status' => 'pending_review']);

        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status'       => 'rejected',
                'rejection_reason' => 'Duplicate complaint.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('complaints', [
            'id'               => $this->complaint->id,
            'status'           => 'rejected',
            'rejection_reason' => 'Duplicate complaint.',
        ]);
    }

    // ── Invalid transitions ───────────────────────────────────────────────

    /** @test */
    public function staff_cannot_skip_status_steps(): void
    {
        // submitted → in_progress is not a valid transition
        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'in_progress',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('complaints', [
            'id'     => $this->complaint->id,
            'status' => 'submitted', // unchanged
        ]);
    }

    /** @test */
    public function staff_cannot_reopen_closed_complaint(): void
    {
        $this->complaint->update(['status' => 'closed']);

        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'submitted',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('complaints', ['id' => $this->complaint->id, 'status' => 'closed']);
    }

    /** @test */
    public function rejection_requires_a_reason(): void
    {
        $this->complaint->update(['status' => 'pending_review']);

        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'rejected',
                // rejection_reason missing
            ])
            ->assertSessionHasErrors('rejection_reason');
    }

    /** @test */
    public function new_status_must_be_a_valid_value(): void
    {
        $this->actingAs($this->staff)
            ->patch("/staff/complaints/{$this->complaint->id}/status", [
                'new_status' => 'hacked_status',
            ])
            ->assertSessionHasErrors('new_status');
    }

    // ── Full happy path: submitted → closed ───────────────────────────────

    /** @test */
    public function full_complaint_lifecycle_submitted_to_closed(): void
    {
        $transitions = [
            'pending_review',
            'validated',
            'in_progress',
            'resolved',
            'closed',
        ];

        $complaint = $this->complaint;

        foreach ($transitions as $next) {
            $this->actingAs($this->staff)
                ->patch("/staff/complaints/{$complaint->id}/status", ['new_status' => $next])
                ->assertRedirect();

            $complaint->refresh();
            $this->assertEquals($next, $complaint->status);
        }
    }
}
