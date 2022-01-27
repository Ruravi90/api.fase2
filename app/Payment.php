<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function user(){
		return $this->hasOne('fase2\User','id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne('fase2\User','id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne('fase2\CatTypeSale','id', 'type_sale_id');
	}
	public function sale(){
		return $this->hasOne('fase2\Sale','id', 'sale_id');
	}
	public function delete()
    {
        return parent::delete();
    }
}
