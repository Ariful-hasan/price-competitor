<?php

use App\Http\Controllers\PingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('api/ping', [PingController::class, 'ping']);

Route::get('api/competitor/v1/product', function () {
    return response()->json([ 
        'product_id' => "124",
        'prices' => [
            ['vendor' => 'Amazon', 'price' => 19.99],
            ['vendor' => 'Walmart', 'price' => 17.49],
            ['vendor' => 'Target', 'price' => 17.49],
        ]
    ]);     
});

Route::get('api/competitor/v2/product', function () {
    return response()->json([
        'id' => "123",
        'competitor_data' => [
            ['name' => 'Metro', 'amount' => 20.09],
            ['name' => 'Kaufland', 'amount' => 16.99],
            ['name' => 'Aldi', 'amount' => 17.39],
        ]
    ]);
});

Route::get('api/competitor/v3/product', function () {
    return response()->json(
        [
            'id' => "123",
            'sku' => 'PROD-2024-ABC-789',
            'status' => 'active',
            'metadata' => [
                'created_at' => '2024-01-15T10:30:00Z',
                'updated_at' => '2024-01-20T14:45:00Z',
                'version' => 2.1,
                'tags' => ['electronics', 'smart-device', 'premium']
            ],
            'competitor_data' => [
                'price_comparison' => [
                    [
                        'competitor_id' => 'comp_metro_001',
                        'name' => 'Metro', 
                        'pricing' => [
                            'current' => 20.09,
                            'original' => 24.99,
                            'currency' => 'EUR',
                            'discount_percentage' => 19.6,
                            'price_history' => [
                                ['date' => '2024-01-01', 'price' => 24.99],
                                ['date' => '2024-01-10', 'price' => 22.49],
                                ['date' => '2024-01-20', 'price' => 20.09]
                            ]
                        ],
                        'availability' => [
                            'status' => true,
                            'stock_level' => 'high',
                            'delivery_time' => '1-2 days',
                            'locations' => ['Berlin', 'Munich', 'Hamburg']
                        ],
                        'metrics' => [
                            'rating' => 4.2,
                            'reviews' => 158,
                            'sentiment' => 'positive',
                            'performance_score' => 88
                        ],
                        'last_updated' => '2024-01-20T12:00:00Z'
                    ],
                    [
                        'competitor_id' => 'comp_kaufland_002',
                        'name' => 'Kaufland', 
                        'pricing' => [
                            'current' => 16.91,
                            'original' => 18.99,
                            'currency' => 'EUR',
                            'discount_percentage' => 10.5,
                            'price_history' => [
                                ['date' => '2024-01-01', 'price' => 18.99],
                                ['date' => '2024-01-15', 'price' => 17.49],
                                ['date' => '2024-01-20', 'price' => 16.99]
                            ]
                        ],
                        'availability' => [
                            'status' => true,
                            'stock_level' => 'medium',
                            'delivery_time' => '2-3 days',
                            'locations' => ['Berlin', 'Cologne', 'Frankfurt']
                        ],
                        'metrics' => [
                            'rating' => 4.0,
                            'reviews' => 89,
                            'sentiment' => 'neutral',
                            'performance_score' => 76
                        ],
                        'last_updated' => '2024-01-20T11:30:00Z'
                    ],
                    [
                        'competitor_id' => 'comp_aldi_003',
                        'name' => 'Aldi', 
                        'pricing' => [
                            'current' => 17.39,
                            'original' => 17.39,
                            'currency' => 'EUR',
                            'discount_percentage' => 0,
                            'price_history' => [
                                ['date' => '2024-01-01', 'price' => 17.39],
                                ['date' => '2024-01-10', 'price' => 17.39],
                                ['date' => '2024-01-20', 'price' => 17.39]
                            ]
                        ],
                        'availability' => [
                            'status' => false,
                            'stock_level' => 'out_of_stock',
                            'delivery_time' => 'unknown',
                            'locations' => []
                        ],
                        'metrics' => [
                            'rating' => 3.8,
                            'reviews' => 67,
                            'sentiment' => 'negative',
                            'performance_score' => 62
                        ],
                        'last_updated' => '2024-01-19T16:15:00Z'
                    ]
                ],
                'summary' => [
                    'total_competitors' => 3,
                    'available_competitors' => 2,
                    'price_trend' => 'decreasing',
                    'market_coverage' => 85.5
                ]
            ],
            'timestamps' => [
                'data_refreshed_at' => '2024-01-20T14:30:00Z',
                'next_update_scheduled' => '2024-01-21T02:00:00Z'
            ],
            'flags' => [
                'needs_attention' => false,
                'price_alert' => true,
                'stock_alert' => false,
                'competition_alert' => true
            ]
        ]
    );
});