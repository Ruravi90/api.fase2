<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/***
 * public id
 * public
 * 
 */
class Sale extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "sales";

	public function client(){
		return $this->hasOne('App\Models\Client','id', 'client_id');
	}
	public function department(){
		return $this->hasOne('App\Models\Department','id', 'department_id');
	}
	public function user(){
		return $this->hasOne('App\Models\User','id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne('App\Models\User','id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne('App\Models\CatTypeSale','id', 'type_sale_id');
	}
	public function cat_package(){
		return $this->hasOne('App\Models\CatPackage','id', 'package_id');
	}
	public function cat_service(){
		return $this->hasOne('App\Models\CatService','id', 'service_id');
	}
	public function cat_pill(){
		return $this->hasOne('App\Models\CatPill','id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne('App\Models\CatProduct','id', 'product_id'); 
	}
	public function sale(){
		return $this->hasOne('App\Models\Sale','id', 'primary_id');
	}

	public function sales(){
		return $this->hasMany(Sale::class, 'primary_id');
	}
	public function payments(){
        return $this->hasMany(Payment::class,'sale_id');
	}
	public function additionals(){
        return $this->hasMany(SaleAdditional::class,'sale_id');
	}
	public function packages(){
        return $this->hasMany(Package::class,'sale_id');
    }

	public function delete()
    {
		$this->sales()->delete();
		$this->payments()->delete();
		$this->additionals()->delete();
		$this->packages()->delete();		
        return parent::delete();
    }
}





