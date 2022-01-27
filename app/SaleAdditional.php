<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class SaleAdditional extends Model
{
    protected $table = "sale_additionals";
	public function sale(){
		return $this->hasOne('fase2\Sale','id', 'sale_id');
    }
    public function cat_pill(){
		return $this->hasOne('fase2\CatPill','id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne('fase2\CatProduct','id', 'product_id'); 
    }
    public function delete()
    {
        return parent::delete();
    }
}
