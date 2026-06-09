<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "departments";


	public function sales(){
        return $this->hasMany(Sale::class);
    }

	public function delete()
    {
        return parent::delete();
    }
}





