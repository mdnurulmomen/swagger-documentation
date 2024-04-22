<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid"=> $this->uuid,
            "first_name"=> $this->first_name,
            "last_name"=> $this->last_name,
            "email"=> $this->email,
            "email_verified_at"=> $this->email_verified_at,
            "avatar"=> $this->avatar,
            "address"=> $this->address,
            "phone_number"=> $this->phone_number,
            "is_marketing"=> $this->is_marketing,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            "last_login_at"=> $this->last_login_at
        ];
    }
}
