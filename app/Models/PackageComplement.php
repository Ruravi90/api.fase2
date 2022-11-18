<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageComplement extends Model
{
    protected $table = "complements_packages";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

    public function cat_package(){
		return $this->hasOne('App\CatPackage','id', 'package_id');
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
