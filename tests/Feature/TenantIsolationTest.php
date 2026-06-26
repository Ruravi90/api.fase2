<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Spatie\Multitenancy\Models\Tenant;

class TestModel extends Model
{
    use \App\Traits\BelongsToTenant;
    protected $table = 'test_models';
    protected $guarded = [];
}

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    /** @test */
    public function it_isolates_data_between_tenants()
    {
        $tenant1 = Tenant::forceCreate(['name' => 'Tenant 1', 'domain' => 'tenant1.local']);
        $tenant2 = Tenant::forceCreate(['name' => 'Tenant 2', 'domain' => 'tenant2.local']);

        $tenant1->makeCurrent();

        TestModel::create(['name' => 'Model of Tenant 1']);

        $this->assertEquals(1, TestModel::count());
        $this->assertEquals('Model of Tenant 1', TestModel::first()->name);

        $tenant2->makeCurrent();
        $this->assertEquals(0, TestModel::count());

        TestModel::create(['name' => 'Model of Tenant 2']);

        $this->assertEquals(1, TestModel::count());
        $this->assertEquals('Model of Tenant 2', TestModel::first()->name);
    }
}
