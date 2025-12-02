<?php

namespace App\Http\Contracts;


interface HttpStreamClientContract
{
    public function stream(string $url): mixed;
}