<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Services\Auth\AuthService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(public AuthService $userService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * User Register function.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->register($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data' => $user,
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User Login/Generate Token function.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->userService->login($request->validated()),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?? 'Something went wrong.'
                ], 
                $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * User Logged Out function.
     *
     * @return void
     */
    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?? 'Something went wrong.'
                ], 
                $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Generate Refresh Token.
     *
     * @return void
     */
    public function refresh(): JsonResponse
    {
        try {
            $refrshToken = $this->userService->refresh();

            return response()->json([
                'success' => true,
                'data'    => $refrshToken,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?? 'Something went wrong, please try again.'
                ], 
                $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
