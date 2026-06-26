<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Tenant;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Tenant::checkCurrent()) {
            $builder->where($model->getTable() . '.tenant_id', Tenant::current()->id);
        }
    }
}
