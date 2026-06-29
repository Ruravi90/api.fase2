<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $guarded = [];

    public function plans()
    {
        return $this->belongsToMany(Plan::class)->withPivot('limit_value');
    }
}
