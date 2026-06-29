<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "departments";


	public function sales(){
        return $this->hasMany(Sale::class);
    }

	public function delete()
    {
        return parent::delete();
    }
}





