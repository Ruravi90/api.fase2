<?php

namespace fase2;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "logs";

	public function delete()
    {
        return parent::delete();
    }
}
