<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryBalance extends Model
{
    protected $table = "history_balance";

	public function delete()
    {
        return parent::delete();
    }
}





