<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
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
                'type' => 'posts',
                'id' => $this->id,
                'attributes' => [
                    'body' => $this->body,
                    'image' => url($this->image),
                    'posted_at' => $this->created_at->diffForHumans(),
                    'posted_by' => new User($this->user),
                    'likes' => new LikeCollection($this->likes),
                    'comments' => new CommentCollection($this->comments)
                ],
            ],
            'links' => [
                'self' => url("/posts/{$this->id}"),
            ]
        ];
    }
}
