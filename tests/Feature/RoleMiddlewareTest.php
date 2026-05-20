<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ── Unauthenticated redirects ─────────────────────────────────────────

    /** @test */
    public function unauthenticated_user_is_redirected_from_citizen_dashboard(): void
    {
        $this->get('/citizen/dashboard')->assertRedirect('/login');
    }

    /** @test */
    public function unauthenticated_user_is_redirected_from_staff_dashboard(): void
    {
        $this->get('/staff/dashboard')->assertRedirect('/login');
    }

    /** @test */
    public function unauthenticated_user_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    // ── Wrong role → 403 ─────────────────────────────────────────────────

    /** @test */
    public function citizen_cannot_access_staff_routes(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);
        $this->actingAs($citizen)->get('/staff/dashboard')->assertForbidden();
    }

    /** @test */
    public function citizen_cannot_access_admin_routes(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);
        $this->actingAs($citizen)->get('/admin/dashboard')->assertForbidden();
    }

    /** @test */
    public function staff_cannot_access_citizen_routes(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get('/citizen/dashboard')->assertForbidden();
    }

    /** @test */
    public function staff_cannot_access_admin_routes(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get('/admin/dashboard')->assertForbidden();
    }

    /** @test */
    public function admin_cannot_access_citizen_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/citizen/dashboard')->assertForbidden();
    }

    /** @test */
    public function admin_cannot_access_staff_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/staff/dashboard')->assertForbidden();
    }

    // ── Correct roles → 200 ──────────────────────────────────────────────

    /** @test */
    public function citizen_can_access_citizen_dashboard(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);
        $this->actingAs($citizen)->get('/citizen/dashboard')->assertOk();
    }

    /** @test */
    public function staff_can_access_staff_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get('/staff/dashboard')->assertOk();
    }

    /** @test */
    public function admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    // ── Deactivated user is logged out ───────────────────────────────────

    /** @test */
    public function deactivated_user_is_logged_out_and_redirected(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen', 'is_active' => false]);
        $response = $this->actingAs($citizen)->get('/citizen/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    // ── Dashboard redirect ────────────────────────────────────────────────

    /** @test */
    public function dashboard_redirects_citizen_to_citizen_dashboard(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);
        $this->actingAs($citizen)->get('/dashboard')->assertRedirect('/citizen/dashboard');
    }

    /** @test */
    public function dashboard_redirects_staff_to_staff_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get('/dashboard')->assertRedirect('/staff/dashboard');
    }

    /** @test */
    public function dashboard_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin/dashboard');
    }
}
