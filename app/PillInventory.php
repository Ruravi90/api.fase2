<?php
namespace fase2;
use Illuminate\Database\Eloquent\Model;

class PillInventory extends Model
{
	protected $table = "pills_inventory";
	//protected $fillable = ['pill_id','count'];

	public function pill(){
		return $this->belongsTo(CatPill::class);
	}

	public function delete()
    {
        return parent::delete();
    }
}
