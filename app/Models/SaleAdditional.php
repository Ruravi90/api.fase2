<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleAdditional extends Model
{
    protected $table = "sale_additionals";
	public function sale(){
		return $this->hasOne(Sale::class,'id', 'sale_id');
    }
    public function cat_pill(){
		return $this->hasOne(CatPill::class,'id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne(CatProduct::class,'id', 'product_id');
    }
    public function delete()
    {
        return parent::delete();
    }
}
