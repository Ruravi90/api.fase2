<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleAdditional extends Model
{
    protected $table = "sale_additionals";
	public function sale(){
		return $this->hasOne('App\Sale','id', 'sale_id');
    }
    public function cat_pill(){
		return $this->hasOne('App\CatPill','id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne('App\CatProduct','id', 'product_id');
    }
    public function delete()
    {
        return parent::delete();
    }
}
