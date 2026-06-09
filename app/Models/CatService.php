<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use App\Models\User;

class CatService extends Model
{
	protected $table = "cat_services";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function sales(){
		return $this->hasMany(Sale::class);
		//return $this->belongsTo(Address::class);
	}

	public function delete()
    {
        return parent::delete();
    }
}





