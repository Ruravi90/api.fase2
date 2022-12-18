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
    protected $table = "sales";

	public function client(){
		return $this->hasOne(Client::class,'id', 'client_id');
	}
	public function department(){
		return $this->hasOne(Department::class,'id', 'department_id');
	}
	public function user(){
		return $this->hasOne(User::class,'id', 'user_id');
	}
	public function responsible(){
		return $this->hasOne(User::class,'id', 'responsible_id');
	}
	public function type(){
		return $this->hasOne(CatTypeSale::class,'id', 'type_sale_id');
	}
	public function cat_package(){
		return $this->hasOne(CatPackage::class,'id', 'package_id');
	}
	public function cat_service(){
		return $this->hasOne(CatService::class,'id', 'service_id');
	}
	public function cat_product(){
		return $this->hasOne(CatProduct::class,'id', 'product_id');
	}
	public function sale(){
		return $this->hasOne(Sale::class,'id', 'primary_id');
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
