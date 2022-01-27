<?php
namespace fase2;
use Illuminate\Database\Eloquent\Model;
//use fase2\User;

class Client extends Model
{
	protected $table = "clients";

	//protected $fillable = ['name','last_name','mother_last_name','email'];

	public function address(){
        //return $this->hasMany(Address::class);
        return $this->hasMany('fase2\Address', 'client_id');
		//return $this->belongsTo(Address::class);
	}

    public function schedules(){
        return $this->hasMany(Schedule::class);
        //return $this->belongsTo(Address::class);
    }

    public function reference(){
		return $this->belongsTo(CatReference::class);
	}

    public function sales(){
        return $this->hasMany(Sale::class);
    }
    
	public function delete()
    {
        // delete all related address 
        $this->address()->delete();
        $this->schedules()->delete();
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // Address::where("client_id", $this->id)->delete()

        // delete the client
        return parent::delete();
    }
}
