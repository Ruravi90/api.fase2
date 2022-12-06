<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use App\User;

class CatPackage extends Model
{
	protected $table = "cat_packages";

	protected $fillable = ['name','price','session_count'];

	public function sales(){
		return $this->hasMany(Sale::class);
	}

	public function packages(){
		return $this->hasMany(Package::class);
	}

	public function complements(){
		return $this->hasMany('App\Models\PackageComplement','package_id');
	}

	public function delete()
    {
        return parent::delete();
    }
}
