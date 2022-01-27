<?php
namespace fase2;
use Illuminate\Database\Eloquent\Model;
//use fase2\User;

class Schedule extends Model
{
	protected $table = "schedule";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function client(){
		return $this->belongsTo(Client::class); 
	}

	public function delete()
    {
        return parent::delete();
    }
}
