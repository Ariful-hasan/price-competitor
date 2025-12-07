<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthService
{
    public function register(array $data)
    {
        try {
            $data['password'] = bcrypt($data['password']);

            return User::create($data);

        } catch (Throwable $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            dd($e->getMessage());
            throw new Exception('Unable to register user.');
        }
    }

    public function login(array $credentials): array
    {
        try {
            $token = auth('api')->attempt($credentials);
            
            if (!$token) {
                throw new AuthenticationException('Email or password is not match.');
            }
            
            return [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
            ];
        } catch (Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $isAuthException = $e instanceof AuthenticationException;

            throw new \Exception(
                $isAuthException ? $e->getMessage() : 'Unable to generate token.',
                $isAuthException ? Response::HTTP_UNAUTHORIZED : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout(): void
    {
        try {
            auth('api')->logout();
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Unable to logout.');
        }
    }

    public function refresh(): array
    {
        try {
            $refeshToken = auth('api')->refresh();
            
            if (!$refeshToken) {
                throw new AuthenticationException('User is not authenticated.');
            }

            return ['refresh_token' => $refeshToken];
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $isAuthException = $e instanceof AuthenticationException;

            throw new \Exception(
                $isAuthException ? $e->getMessage() : 'Unable to generate refresh token.',
                $isAuthException ? Response::HTTP_UNAUTHORIZED : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}