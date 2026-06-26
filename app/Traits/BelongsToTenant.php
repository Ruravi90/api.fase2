<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Spatie\Multitenancy\Models\Tenant;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (!$model->tenant_id && Tenant::checkCurrent()) {
                $model->tenant_id = Tenant::current()->id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
