<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class PackageComplement extends Model
{
    protected $table = "complements_packages";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

    public function cat_package(){
		return $this->hasOne('fase2\CatPackage','id', 'package_id');
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
