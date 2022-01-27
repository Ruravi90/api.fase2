<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class CatTypeSale extends Model
{
    protected $table = "cat_type_sales";

	//protected $fillable = ['name','price'];

	public function sales(){
		return $this->hasMany(Sale::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}
