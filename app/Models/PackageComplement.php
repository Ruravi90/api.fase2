<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageComplement extends Model
{
    protected $table = "complements_packages";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

    public function cat_package(){
		return $this->hasOne(CatPackage::class,'id', 'package_id');
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
