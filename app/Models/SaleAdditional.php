<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleAdditional extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "sale_additionals";
	public function sale(){
		return $this->hasOne('App\Models\Sale','id', 'sale_id');
    }
    public function cat_pill(){
		return $this->hasOne('App\Models\CatPill','id', 'pill_id');
	}
	public function cat_product(){
		return $this->hasOne('App\Models\CatProduct','id', 'product_id'); 
    }
    public function delete()
    {
        return parent::delete();
    }
}





