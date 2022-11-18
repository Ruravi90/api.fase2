<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use App\User;

class Provider extends Model
{
	protected $table = "providers";

	//protected $fillable = ['business_name','contact_name','office_phone','email'];

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
