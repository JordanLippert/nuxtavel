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
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->service->listUsers([
            'search' => $request->query('search'),
            'role'   => $request->query('role'),
            'status' => $request->query('status'),
        ]);

        return UserResource::collection($users);
    }

    public function export(Request $request): StreamedResponse
    {
        $users    = $this->service->exportUsers([
            'search' => $request->query('search'),
            'role'   => $request->query('role'),
            'status' => $request->query('status'),
        ]);
        $filename = 'usuarios_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 para o Excel abrir corretamente
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Nome', 'E-mail', 'Nascimento', 'Papel', 'Status', 'Criado em'], ';');
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->birth_date?->format('d/m/Y') ?? '',
                    $user->role   ?? '',
                    $user->status ?? '',
                    $user->created_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
