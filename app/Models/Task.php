<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "tasks";
	public function user(){
		return $this->belongsTo(User::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}





