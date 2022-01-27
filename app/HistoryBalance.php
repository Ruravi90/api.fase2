<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class HistoryBalance extends Model
{
    protected $table = "history_balance";

	public function delete()
    {
        return parent::delete();
    }
}
