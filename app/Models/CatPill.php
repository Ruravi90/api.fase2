<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CatPill extends Model
{
    protected $table = "cat_pills";

	//protected $fillable = ['name','price'];

	public function sales(){
		return $this->hasMany(Sale::class);
	}

	public function inventory(){
		return $this->hasMany('App\PillInventory','pill_id');
	}

	public function delete()
    {
        return parent::delete();
    }
}
