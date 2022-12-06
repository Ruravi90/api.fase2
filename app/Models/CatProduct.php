<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CatProduct extends Model
{
	protected $table = "cat_products";

	protected $fillable = ['name'];

	public function sales(){
		return $this->hasMany(Sale::class);
	}

	public function inventory(){
		return $this->hasMany('App\Models\ProductInventory','product_id');
	}

	public function delete()
    {
    	$this->sales()->delete();
        $this->inventary()->delete();
        return parent::delete();
    }
}

