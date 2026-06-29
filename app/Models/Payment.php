<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "payments";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function user(){
		return $this->hasOne('App\Models\User','id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne('App\Models\User','id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne('App\Models\CatTypeSale','id', 'type_sale_id');
	}
	public function sale(){
		return $this->hasOne('App\Models\Sale','id', 'sale_id');
	}
	public function delete()
    {
        return parent::delete();
    }
}





