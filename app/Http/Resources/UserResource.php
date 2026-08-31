<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'franchise_id' => $this->franchise_id,
            'franchise' => $this->whenLoaded('franchise', fn () => $this->franchise ? [
                'id' => $this->franchise->id,
                'name' => $this->franchise->name,
            ] : null),
            'roles' => $this->getRoleNames(), // from Spatie\Permission\Traits\HasRoles
            'mobile_verified' => $this->mobile_verified_at !== null,
            'is_active' => $this->is_active,
        ];
    }
}
