<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class PackageTracking extends Model
{
    protected $table = "package_tracking";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

    public function package(){
		return $this->belongsTo(Package::class); 
	}

	public function user(){
		return $this->hasOne('fase2\User','id', 'user_id');
	}

	public function delete()
    {
        return parent::delete();
    }
}
