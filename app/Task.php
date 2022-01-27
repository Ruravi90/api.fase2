<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = "tasks";
	public function user(){
		return $this->belongsTo(User::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}
