<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchases";

	//protected $fillable = ['business_name','contact_name','office_phone','email'];

    public function department(){
		return $this->hasOne('fase2\Department','id', 'department_id');
	}

	public function provider(){
		return $this->hasOne('fase2\Provider','id', 'provider_id');
	}

	public function user(){
		return $this->hasOne('fase2\User','id', 'user_id');
	}

    public function cat_pill(){
		return $this->hasOne('fase2\CatPill','id', 'pill_id');
	}

	public function cat_product(){
		return $this->hasOne('fase2\CatProduct','id', 'product_id'); 
	}

	public function cat_expense(){
		return $this->hasOne('fase2\CatExpense','id', 'expence_id'); 
	}

	public function cat_concept(){
		return $this->hasOne('fase2\CatConcept','id', 'concept_id'); 
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
