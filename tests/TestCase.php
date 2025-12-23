<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function authenticated(string $method, string $uri, string $token, array $data = []) : TestResponse 
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json($method, $uri, $data);
    }
}
