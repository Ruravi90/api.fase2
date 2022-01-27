<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

//use fase2\User;

class CatProduct extends Model
{
	protected $table = "cat_products";

	protected $fillable = ['name'];

	public function sales(){
		return $this->hasMany(Sale::class);
	}

	public function inventory(){
		return $this->hasMany('fase2\ProductInventory','product_id');
	}

	public function delete()
    { 
    	$this->sales()->delete();
        $this->inventary()->delete();
        return parent::delete();
    }
}

