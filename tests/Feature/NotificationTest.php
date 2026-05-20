<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $citizen;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->citizen  = User::factory()->create(['role' => 'citizen']);
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function citizen_can_view_notifications_page(): void
    {
        $this->actingAs($this->citizen)->get('/citizen/notifications')->assertOk();
    }

    /** @test */
    public function viewing_notifications_page_marks_all_as_read(): void
    {
        $complaint = Complaint::factory()->create([
            'user_id'     => $this->citizen->id,
            'category_id' => $this->category->id,
        ]);

        Notification::create([
            'user_id'      => $this->citizen->id,
            'complaint_id' => $complaint->id,
            'message'      => 'Your complaint was updated.',
            'is_read'      => false,
        ]);

        $this->actingAs($this->citizen)->get('/citizen/notifications');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->citizen->id,
            'is_read' => true,
        ]);
    }

    /** @test */
    public function mark_all_read_works_via_post(): void
    {
        $complaint = Complaint::factory()->create([
            'user_id'     => $this->citizen->id,
            'category_id' => $this->category->id,
        ]);

        Notification::create([
            'user_id'      => $this->citizen->id,
            'complaint_id' => $complaint->id,
            'message'      => 'Update.',
            'is_read'      => false,
        ]);

        $this->actingAs($this->citizen)
            ->post('/citizen/notifications/mark-all-read')
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->citizen->id,
            'is_read' => true,
        ]);
    }

    /** @test */
    public function citizen_cannot_mark_another_users_notification_as_read(): void
    {
        $other     = User::factory()->create(['role' => 'citizen']);
        $complaint = Complaint::factory()->create([
            'user_id'     => $other->id,
            'category_id' => $this->category->id,
        ]);
        $notification = Notification::create([
            'user_id'      => $other->id,
            'complaint_id' => $complaint->id,
            'message'      => 'Notification for other user.',
            'is_read'      => false,
        ]);

        $this->actingAs($this->citizen)
            ->get("/citizen/notifications/{$notification->id}/read")
            ->assertForbidden();
    }
}
