<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatExpense extends Model
{
    use \App\Traits\BelongsToTenant;

     protected $table = "cat_expenses";
     protected $fillable = ['name'];

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function purchases(){
		return $this->hasMany(Purchase::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}





