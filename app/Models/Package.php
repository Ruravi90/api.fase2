<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = "packages";

    protected $casts = [
	    'is_taken' => 'boolean',
	];
	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function client(){
		return $this->belongsTo(Client::class);
	}

	public function sale(){
		return $this->belongsTo(Sale::class);
	}

	public function type(){
		return $this->hasOne(CatPackage::class,'id', 'cat_package_id');
	}

	public function tracking(){
		return $this->hasMany(PackageTracking::class);
	}

	public function delete()
    {
		$this->tracking()->delete();
        return parent::delete();
    }
}
