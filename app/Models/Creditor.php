<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use App\Models\User;

class Creditor extends Model
{
    use \App\Traits\BelongsToTenant;

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





