<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatConcept extends Model
{
    use \App\Traits\BelongsToTenant;

   	protected $table = "cat_concepts";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function purchases(){
		return $this->hasMany(Purchase::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}





