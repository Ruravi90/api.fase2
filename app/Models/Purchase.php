<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchases";

	//protected $fillable = ['business_name','contact_name','office_phone','email'];

    public function department(){
		return $this->hasOne(Department::class,'id', 'department_id');
	}

	public function provider(){
		return $this->hasOne(Provider::class,'id', 'provider_id');
	}

	public function user(){
		return $this->hasOne(User::class,'id', 'user_id');
	}

	public function cat_product(){
		return $this->hasOne(CatProduct::class,'id', 'product_id');
	}

	public function cat_expense(){
		return $this->hasOne(CatExpense::class,'id', 'expence_id');
	}

	public function cat_concept(){
		return $this->hasOne(CatConcept::class,'id', 'concept_id');
	}

	public function purchases(){
		return $this->hasMany(Purchase::class, 'purchase_id');
	}

	public function delete()
    {
		$this->purchases()->delete();
        return parent::delete();
    }
}
