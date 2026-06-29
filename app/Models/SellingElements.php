<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellingElements extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = "selling_elements";

	//protected $fillable = ['name','price'];

	public function sale(){
		return $this->belongsTo(Sale::class); 
	}
	public function package(){
		return $this->belongsTo(CatPackage::class); 
	}
	public function pill(){
		return $this->belongsTo(CatPill::class); 
	}
	public function product(){
		return $this->belongsTo(CatProduct::class); 
	}
	public function service(){
		return $this->belongsTo(CatService::class); 
	}
	public function delete()
    {
        return parent::delete();
    }
}





