<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $table = "products_inventory";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function product(){
		return $this->belongsTo(CatProduct::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}
