<?php 

namespace Tests\Feature;

use App\Http\Services\Product\ProductService;
use App\Models\ProductLowestPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = FacadesJWTAuth::fromUser($this->user);
    }

    public function test_get_product_list_with_not_authorized_response(): void
    {
        $response = $this->authenticated('GET', '/api/products', 'invalid_token');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthenticated.', $responseData['message']);
    }

    public function test_get_product_list_returns_successful_response(): void
    {
        $mockData = ProductLowestPrice::factory()->count(5)->make();

        $response = $this->authenticated('GET', '/api/products', $this->token);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => ['*' => ['product_id', 'vendor', 'price', 'fetched_at']],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            'success'
        ]);
    }

    public function test_get_product_list_when_exception_is_thrown(): void
    {
        $this->mock(ProductService::class, function ($mock) {
            $mock->shouldReceive('getLowestProductList')
                ->andThrow(new \Exception('Unable to fetch the product list.'));
        });

        $response = $this->authenticated('GET', '/api/products', $this->token);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unable to fetch the product list.', $responseData['message']);
    }

    public function test_show_product_when_not_authorized_response(): void
    {
        $response = $this->authenticated('GET', '/api/products/1', 'invalid_token');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthenticated.', $responseData['message']);
    }

    public function test_show_product_returns_successful_response(): void
    {
        $product = ProductLowestPrice::factory()->create();

        $response = $this->authenticated('GET', '/api/products/' . $product->product_id, $this->token);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => ['product_id', 'vendor', 'price', 'fetched_at'],
            'success'
        ]);
    }

    public function test_show_product_when_product_not_found_response(): void
    {
        $response = $this->authenticated('GET', '/api/products/999', $this->token);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Product not found.', $responseData['message']);
    }

    public function test_show_product_when_exception_is_thrown(): void
    {
        $this->mock(ProductService::class, function ($mock) {
            $mock->shouldReceive('getLowestPriceProductById')
                ->andThrow(new \Exception('Unable to fetch the product list.', Response::HTTP_INTERNAL_SERVER_ERROR));
        });

        $response = $this->authenticated('GET', '/api/products/1', $this->token);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unable to fetch the product list.', $responseData['message']);
    }

}