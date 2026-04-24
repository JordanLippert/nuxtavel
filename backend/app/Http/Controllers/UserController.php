<?php

namespace App\Http\Controllers;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->service->listUsers([
            'search' => $request->query('search'),
        ]);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->createUser(CreateUserDTO::fromRequest($request));

        return response()->json(new UserResource($user), 201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $updated = $this->service->updateUser($user, UpdateUserDTO::fromRequest($request));

        return new UserResource($updated);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->service->deleteUser($user);

        return response()->json(null, 204);
    }
}
