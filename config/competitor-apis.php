<?php


return [
    [
        'url' => 'http://192.168.178.27:8080/api/competitor/v1/product',
        'json_pointer' => '/prices',
        'product_id' => '124'
    ],
    [
        'url' => 'http://192.168.178.27:8080/api/competitor/v2/product',
        'json_pointer' => '/competitor_data',
        'product_id' => '123'
    ],
    [
        'url' => 'http://192.168.178.27:8080/api/competitor/v3/product',
        'json_pointer' =>  '/competitor_data/price_comparison',
        'product_id' => '123'
    ]
];