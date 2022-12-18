<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function user(){
		return $this->hasOne(User::class,'id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne(User::class,'id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne(CatTypeSale::class,'id', 'type_sale_id');
	}
	public function sale(){
		return $this->hasOne(Sale::class,'id', 'sale_id');
	}
	public function delete()
    {
        return parent::delete();
    }
}
