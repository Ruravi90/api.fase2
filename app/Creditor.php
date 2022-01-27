<?php
namespace fase2;
use Illuminate\Database\Eloquent\Model;
//use fase2\User;

class Creditor extends Model
{
	protected $table = "creditors";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function address(){
		return $this->hasMany(Address::class);
		//return $this->belongsTo(Address::class);
	}

	public function delete()
    {
    	// delete all related address 
        $this->address()->delete(); 
        return parent::delete();
    }
}
