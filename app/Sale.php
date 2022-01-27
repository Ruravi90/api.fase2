<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;
/***
 * public id
 * public
 * 
 */
class Sale extends Model
{
    protected $table = "sales";

	public function client(){
		return $this->hasOne('fase2\Client','id', 'client_id');
	}
	public function department(){
		return $this->hasOne('fase2\Department','id', 'department_id');
	}
	public function user(){
		return $this->hasOne('fase2\User','id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne('fase2\User','id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne('fase2\CatTypeSale','id', 'type_sale_id');
	}
	public function cat_package(){
		return $this->hasOne('fase2\CatPackage','id', 'package_id');
	}
	public function cat_service(){
		return $this->hasOne('fase2\CatService','id', 'service_id');
	}
	public function cat_pill(){
		return $this->hasOne('fase2\CatPill','id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne('fase2\CatProduct','id', 'product_id'); 
	}
	public function sale(){
		return $this->hasOne('fase2\Sale','id', 'primary_id');
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
