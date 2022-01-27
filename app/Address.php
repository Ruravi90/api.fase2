<?php
namespace fase2;
use Illuminate\Database\Eloquent\Model;
//use fase2\User;

class Address extends Model
{
	protected $table = "address";

	public function client(){
		return $this->belongsTo(Client::class);
	}

	public function creditor(){
		return $this->belongsTo(Creditor::class);
	}

	public function provider(){
		return $this->belongsTo(Provider::class);
	}

	public function delete()
    {
        return parent::delete();
    }
}

