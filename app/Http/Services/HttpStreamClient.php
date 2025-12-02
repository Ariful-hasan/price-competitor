<?php

namespace App\Http\Services;

use App\Http\Contracts\HttpStreamClientContract;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException as ClientRequestException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class HttpStreamClient implements HttpStreamClientContract
{
    private int $timeout;
    private int $retries;

    public function __construct() 
    {
        $this->timeout = config("services.http_stream.timeout");
        $this->retries = config("services.http_stream.retries");
    }

    /**
     * @return resource|null
     */
    public function stream(string $url): mixed
    {
        try {
            $response = Http::timeout($this->timeout)
            ->retry($this->retries)
            ->withOptions(['stream' => true])
            ->get($url);

            $response->throw();

            $stream = $response->toPsrResponse()->getBody();
        
            return $stream->detach();
        } catch (ConnectionException $e) {
            throw new HttpException(Response::HTTP_REQUEST_TIMEOUT, "Stream connection to {$url} timed out after {$this->timeout} seconds");
        } catch (ClientRequestException $e) {
            $statusCode = $e->response->status();
            $body = $e->response->body();
            $message = "HTTP {$statusCode} error while streaming from {$url}";
            
            if (!empty($body)) {
                $message .= " - Response: " . substr($body, 0, 100);
            }
            
            throw new HttpException($statusCode, $message);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, "Network error while streaming from {$url}: {$e->getMessage()}");
        }
    }
}