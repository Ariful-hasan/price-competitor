<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class PingController extends Controller
{
    public function ping(): Response
    {
        $test = Redis::set('ping:msg', 'Hello World!!!');

        return response()->json([
            'data' => [
                'message' => Redis::get('ping:msg', 'Not Found in redis!!!'),
                'timestamp' => now()->toISOString()
            ]
        ], 200);
    }
}