<?php

namespace App\Http\Clients;

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
     * Stream the response body from a given URL using HTTP with retry and timeout options.
     *
     * Initiates a streamed HTTP GET request to the provided URL. Returns a raw stream resource 
     * suitable for consumption by a streaming JSON parser or similar use case. 
     * Handles network and HTTP errors by throwing meaningful HttpException instances.
     *
     * @param string $url The URL from which to stream content.
     * 
     * @return resource The detached stream resource handle for the response body.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *         If there's a timeout, HTTP error, or other network/streaming failure.
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