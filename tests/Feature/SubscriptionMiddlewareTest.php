<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Spatie\Multitenancy\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;

class SubscriptionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Define a test route protected by the middleware
        Route::get('/test-protected', function () {
            return response()->json(['message' => 'Success']);
        })->middleware(\App\Http\Middleware\CheckSubscription::class);
    }

    /** @test */
    public function it_blocks_access_if_no_tenant_is_current()
    {
        $response = $this->get('/test-protected');
        $response->assertStatus(403);
        $response->assertJson(['message' => 'No tenant context found']);
    }

    /** @test */
    public function it_blocks_access_if_tenant_has_no_subscription()
    {
        $tenant = Tenant::forceCreate(['name' => 'Test Tenant', 'domain' => 'test.local']);
        $tenant->makeCurrent();

        $response = $this->get('/test-protected');
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Subscription expired or inactive. Please renew your plan.']);
    }

    /** @test */
    public function it_allows_access_if_subscription_is_active()
    {
        $tenant = Tenant::forceCreate(['name' => 'Test Tenant', 'domain' => 'test.local']);
        $tenant->makeCurrent();

        $plan = Plan::create([
            'name' => 'Test Plan',
            'price' => 100,
            'billing_cycle' => 'monthly',
            'is_active' => true
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addDays(30)
        ]);

        $response = $this->get('/test-protected');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);
    }

    /** @test */
    public function it_blocks_access_if_subscription_is_expired()
    {
        $tenant = Tenant::forceCreate(['name' => 'Test Tenant', 'domain' => 'test.local']);
        $tenant->makeCurrent();

        $plan = Plan::create([
            'name' => 'Test Plan',
            'price' => 100,
            'billing_cycle' => 'monthly',
            'is_active' => true
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active', // status is active but date passed
            'ends_at' => now()->subDays(1)
        ]);

        $response = $this->get('/test-protected');
        $response->assertStatus(403);
    }
}
