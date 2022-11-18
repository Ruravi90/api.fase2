<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function user(){
		return $this->hasOne('App\User','id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne('App\User','id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne('App\CatTypeSale','id', 'type_sale_id');
	}
	public function sale(){
		return $this->hasOne('App\Sale','id', 'sale_id');
	}
	public function delete()
    {
        return parent::delete();
    }
}
