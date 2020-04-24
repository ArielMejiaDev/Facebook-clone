<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Friend extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $this->id,
                'attributes' => [
                    'user_id'      => $this->user_id,
                    'friend_id'    => $this->friend_id,
                    'confirmed_at' => optional($this->confirmed_at)->diffForHumans(),
                ]
            ],
            'links' => [
                'self' => url("/users/{$this->friend_id}")
            ]
        ];
    }
}
