<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = "companies";

    public function sales(){
		return $this->hasMany(Sale::class);
		//return $this->belongsTo(Address::class);
	}

	public function delete()
    {
        return parent::delete();
    }
}
