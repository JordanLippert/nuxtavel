<?php

namespace App\Http\Controllers;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private UserService $service) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $dto  = LoginDTO::fromRequest($request);
        $user = \App\Models\User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user  = $this->service->registerUser(RegisterDTO::fromRequest($request));
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
