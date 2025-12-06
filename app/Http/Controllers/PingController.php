<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class PingController extends Controller
{
    public function ping(): Response
    {
        $str = "Eqfkpi Vguv ku Ejanngpikpi!";
        //$str = 'Coding Test is Challenging!';
        
        
        $decryptedStr = '';

        for($i=0; $i<strlen($str); $i++) {
            $chrVal = ord($str[$i]);
            
            if ($chrVal >= 65 && $chrVal <= 90) {
                $newChrVal = $chrVal == 65 ? 90 : ($chrVal == 90 ? 65 : null);
                $decryptedStr .= $newChrVal ? chr($chrVal) : chr($chrVal - 2);
            } else if ($chrVal >= 97 && $chrVal <= 122) {
                $newChrVal = $chrVal == 97 ? 122 : ($chrVal == 122 ? 97 : null);
                $decryptedStr .= $newChrVal ? chr($chrVal) : chr($chrVal - 2);
            } else {
                $decryptedStr .= $str[$i];
            }
        }

        dd($decryptedStr);




        $test = Redis::set('ping:msg', 'Hello World!!!');

        return response()->json([
            'data' => [
                'message' => Redis::get('ping:msg', 'Not Found in redis!!!'),
                'timestamp' => now()->toISOString()
            ]
        ], 200);
    }
}