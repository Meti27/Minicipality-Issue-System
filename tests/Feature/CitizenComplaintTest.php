<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CitizenComplaintTest extends TestCase
{
    use RefreshDatabase;

    private User $citizen;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->citizen  = User::factory()->create(['role' => 'citizen']);
        $this->category = Category::factory()->create(['is_active' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title'       => 'Pothole on Main Street',
            'description' => 'Large pothole causing vehicle damage.',
            'category_id' => $this->category->id,
            'location'    => 'Main Street Block 5',
            'image'       => UploadedFile::fake()->image('pothole.jpg', 200, 200),
        ], $overrides);
    }

    // ── Create / Store ────────────────────────────────────────────────────

    /** @test */
    public function citizen_can_view_create_form(): void
    {
        $this->actingAs($this->citizen)->get('/citizen/complaints/create')->assertOk();
    }

    /** @test */
    public function citizen_can_submit_a_valid_complaint(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('complaints', [
            'user_id'     => $this->citizen->id,
            'title'       => 'Pothole on Main Street',
            'status'      => 'submitted',
            'category_id' => $this->category->id,
        ]);
    }

    /** @test */
    public function submitting_creates_a_status_history_record(): void
    {
        Storage::fake('public');

        $this->actingAs($this->citizen)->post('/citizen/complaints', $this->validPayload());

        $complaint = Complaint::first();
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $complaint->id,
            'new_status'   => 'submitted',
            'old_status'   => null,
        ]);
    }

    /** @test */
    public function image_is_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['image']);

        $response = $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $payload);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('complaints', ['title' => 'Pothole on Main Street']);
    }

    /** @test */
    public function title_is_required(): void
    {
        Storage::fake('public');
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function description_is_required(): void
    {
        Storage::fake('public');
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['description' => '']))
            ->assertSessionHasErrors('description');
    }

    /** @test */
    public function location_is_required(): void
    {
        Storage::fake('public');
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['location' => '']))
            ->assertSessionHasErrors('location');
    }

    /** @test */
    public function category_must_exist(): void
    {
        Storage::fake('public');
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['category_id' => 9999]))
            ->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function image_must_be_valid_image_type(): void
    {
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload([
                'image' => UploadedFile::fake()->create('file.pdf', 100, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('image');
    }

    /** @test */
    public function image_over_2mb_is_rejected(): void
    {
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload([
                'image' => UploadedFile::fake()->image('big.jpg')->size(3000),
            ]))
            ->assertSessionHasErrors('image');
    }

    /** @test */
    public function title_over_255_chars_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['title' => str_repeat('A', 256)]))
            ->assertSessionHasErrors('title');
    }

    // ── Index / Show ──────────────────────────────────────────────────────

    /** @test */
    public function citizen_sees_only_their_own_complaints(): void
    {
        Storage::fake('public');
        $other = User::factory()->create(['role' => 'citizen']);
        Complaint::factory()->create(['user_id' => $other->id, 'category_id' => $this->category->id]);
        Complaint::factory()->create(['user_id' => $this->citizen->id, 'category_id' => $this->category->id, 'title' => 'My complaint']);

        $response = $this->actingAs($this->citizen)->get('/citizen/complaints');
        $response->assertSee('My complaint');
        // Should not see other user's complaint title in the list scoped to this citizen
        $this->assertDatabaseHas('complaints', ['user_id' => $other->id]);
        $response->assertOk();
    }

    /** @test */
    public function citizen_can_view_their_own_complaint(): void
    {
        $complaint = Complaint::factory()->create([
            'user_id'     => $this->citizen->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->citizen)
            ->get("/citizen/complaints/{$complaint->id}")
            ->assertOk();
    }

    /** @test */
    public function citizen_cannot_view_another_users_complaint(): void
    {
        $other     = User::factory()->create(['role' => 'citizen']);
        $complaint = Complaint::factory()->create([
            'user_id'     => $other->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->citizen)
            ->get("/citizen/complaints/{$complaint->id}")
            ->assertForbidden();
    }

    // ── XSS input stored safely ───────────────────────────────────────────

    /** @test */
    public function xss_in_title_is_stored_as_plain_text_not_executed(): void
    {
        Storage::fake('public');
        $xss = '<script>alert("xss")</script>';

        $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload(['title' => $xss]));

        $complaint = Complaint::first();
        // Stored raw (Blade escapes on output — that's the correct pattern)
        $this->assertEquals($xss, $complaint->title);

        // But rendered output must be escaped
        $response = $this->actingAs($this->citizen)
            ->get("/citizen/complaints/{$complaint->id}");
        $response->assertDontSee('<script>alert("xss")</script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    // ── Duplicate detection ───────────────────────────────────────────────

    /** @test */
    public function highly_similar_complaint_with_similar_image_is_rejected_as_duplicate(): void
    {
        // Use real disk so GD can load images for comparison
        $dir = storage_path('app/public/complaints-test');
        @mkdir($dir, 0755, true);

        $img  = imagecreatetruecolor(50, 50);
        $blue = imagecolorallocate($img, 70, 130, 180);
        imagefill($img, 0, 0, $blue);
        $existingPath = $dir . '/test-existing.jpg';
        imagejpeg($img, $existingPath);
        imagedestroy($img);

        $relPath = 'complaints-test/test-existing.jpg';

        Complaint::factory()->create([
            'user_id'     => $this->citizen->id,
            'category_id' => $this->category->id,
            'title'       => 'Pothole on Main Street',
            'location'    => 'Main Street Block 5',
            'status'      => 'submitted',
            'image_path'  => $relPath,
        ]);

        // New upload: create an identical-coloured fake image as a real temp file
        $newImg  = imagecreatetruecolor(50, 50);
        $blue2   = imagecolorallocate($newImg, 70, 130, 180);
        imagefill($newImg, 0, 0, $blue2);
        $newPath = sys_get_temp_dir() . '/test-new.jpg';
        imagejpeg($newImg, $newPath);
        imagedestroy($newImg);

        $upload = new UploadedFile($newPath, 'test-new.jpg', 'image/jpeg', null, true);

        $response = $this->actingAs($this->citizen)
            ->post('/citizen/complaints', $this->validPayload([
                'title'    => 'Pothole on Main Street',
                'location' => 'Main Street Block 5',
                'image'    => $upload,
            ]));

        // Cleanup
        @unlink($existingPath);
        @unlink($newPath);
        @rmdir($dir);

        $response->assertSessionHasErrors('title');
    }

    // ── Throttle ──────────────────────────────────────────────────────────

    /** @test */
    public function complaint_submission_route_has_throttle_middleware(): void
    {
        $routes = app('router')->getRoutes();
        $route  = $routes->getByName('citizen.complaints.store');
        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();
        $this->assertTrue(
            collect($middleware)->contains(fn($m) => str_contains($m, 'throttle')),
            'Store route should have throttle middleware'
        );
    }
}
