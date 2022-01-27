<?php

namespace fase2\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Focades\Redis;

class QueueController extends Controller
{
    public function index()
    {
        Redis::set();
        Redis::FLUSHALL();
    }
}
