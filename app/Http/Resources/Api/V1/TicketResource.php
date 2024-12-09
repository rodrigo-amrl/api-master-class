<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'type' => 'ticket',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'description' => $this->when($request->routeIs('tickets.show'), $this->description),
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'links' => [
                'self' => route('tickets.show', $this->id),
            ],
            'relationships' => [
                'author' => [
                    'links' => [
                        'self' => route('users.show', $this->user_id),
                    ],
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id
                    ]
                ]
            ],
            'includes' =>  new UserResource($this->whenLoaded('user'))

        ];
    }
}
