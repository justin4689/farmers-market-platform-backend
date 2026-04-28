<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->login($request->validated());

            return $this->success([
                'token' => $result['token'],
                'user'  => new UserResource($result['user']),
            ], 'Login successful.');
        } catch (InvalidCredentialsException $e) {
            return $this->error($e->getMessage(), [], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());

        return $this->success([], 'Logged out successfully.');
    }
}
