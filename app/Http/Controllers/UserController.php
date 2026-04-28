<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOperatorRequest;
use App\Http\Requests\CreateSupervisorRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function createSupervisor(CreateSupervisorRequest $request): JsonResponse
    {
        $user = $this->userService->createSupervisor(
            $request->validated(),
            $request->user()->id
        );

        return $this->success(new UserResource($user), 'Supervisor created successfully.', 201);
    }

    public function createOperator(CreateOperatorRequest $request): JsonResponse
    {
        $user = $this->userService->createOperator(
            $request->validated(),
            $request->user()->id
        );

        return $this->success(new UserResource($user), 'Operator created successfully.', 201);
    }
}
