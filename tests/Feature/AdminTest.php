<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ── Dashboard ─────────────────────────────────────────────────────────

    /** @test */
    public function admin_dashboard_loads_successfully(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
    }

    // ── User management ───────────────────────────────────────────────────

    /** @test */
    public function admin_can_list_users(): void
    {
        $this->actingAs($this->admin)->get('/admin/users')->assertOk();
    }

    /** @test */
    public function admin_can_search_users(): void
    {
        $this->actingAs($this->admin)->get('/admin/users?search=john')->assertOk();
    }

    /** @test */
    public function admin_can_filter_users_by_role(): void
    {
        $this->actingAs($this->admin)->get('/admin/users?role=citizen')->assertOk();
    }

    /** @test */
    public function admin_can_create_a_citizen_user(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'                  => 'Test Citizen',
                'email'                 => 'newcitizen@test.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'citizen',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['email' => 'newcitizen@test.com', 'role' => 'citizen']);
    }

    /** @test */
    public function admin_can_create_a_staff_user(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'                  => 'Test Staff',
                'email'                 => 'newstaff@test.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'staff',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['email' => 'newstaff@test.com', 'role' => 'staff']);
    }

    /** @test */
    public function admin_cannot_create_another_admin(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'                  => 'Bad Admin',
                'email'                 => 'badmin@test.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'admin',
            ])
            ->assertSessionHasErrors('role');
    }

    /** @test */
    public function creating_user_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name'                  => 'Duplicate',
                'email'                 => 'existing@test.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'citizen',
            ])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function admin_can_deactivate_a_citizen(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$citizen->id}/toggle-active")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $citizen->id, 'is_active' => false]);
    }

    /** @test */
    public function admin_can_reactivate_a_citizen(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen', 'is_active' => false]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$citizen->id}/toggle-active")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $citizen->id, 'is_active' => true]);
    }

    /** @test */
    public function admin_cannot_deactivate_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->admin->id}/toggle-active")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'is_active' => true]);
    }

    /** @test */
    public function admin_cannot_deactivate_another_admin(): void
    {
        $admin2 = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$admin2->id}/toggle-active")
            ->assertRedirect();

        // Should still be active
        $this->assertDatabaseHas('users', ['id' => $admin2->id, 'is_active' => true]);
    }

    // ── Category management ───────────────────────────────────────────────

    /** @test */
    public function admin_can_list_categories(): void
    {
        $this->actingAs($this->admin)->get('/admin/categories')->assertOk();
    }

    /** @test */
    public function admin_can_create_a_category(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/categories', [
                'name'        => 'Fallen Trees',
                'description' => 'Issues related to fallen trees.',
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', ['name' => 'Fallen Trees', 'is_active' => true]);
    }

    /** @test */
    public function category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Potholes']);

        $this->actingAs($this->admin)
            ->post('/admin/categories', ['name' => 'Potholes'])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_edit_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put("/admin/categories/{$category->id}", [
                'name'        => 'New Name',
                'description' => 'Updated description.',
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    /** @test */
    public function admin_can_toggle_category_active_status(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->patch("/admin/categories/{$category->id}/toggle-active")
            ->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
    }

    // ── Input validation ──────────────────────────────────────────────────

    /** @test */
    public function user_creation_requires_name(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', ['name' => '', 'email' => 'x@x.com', 'password' => 'Abc123!', 'password_confirmation' => 'Abc123!', 'role' => 'citizen'])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function user_creation_requires_valid_email(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', ['name' => 'A', 'email' => 'not-an-email', 'password' => 'Abc123!', 'password_confirmation' => 'Abc123!', 'role' => 'citizen'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_creation_requires_password_confirmation(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/users', ['name' => 'A', 'email' => 'a@a.com', 'password' => 'Abc123!', 'password_confirmation' => 'different', 'role' => 'citizen'])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function category_name_over_255_chars_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/categories', ['name' => str_repeat('X', 256)])
            ->assertSessionHasErrors('name');
    }
}
