<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'birth_date' => $this->birth_date?->format('d/m/Y'),
            'avatar_url' => $this->avatar ? Storage::url($this->avatar) : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
