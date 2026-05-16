<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return [
        'id' => $this->id,
        'content' => $this->content,
        'type' => $this->type,

        'user' => [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'image' => $this->user->image
            ? asset('storage/' . $this->user->image)
            : null,
        ],

        'likes_count' => $this->likes_count,
        'comments_count' => $this->comments_count,
        'views_count' => $this->views_count,
        'comments' => $this->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'image' =>asset('storage/' . $comment->user->image)

                ],

                'created_at' => $comment->created_at->diffForHumans(),
            ];
        }),
        'is_liked' => (bool) $this->is_liked,

        'created_at' => $this->created_at->diffForHumans(),
    ];    }
}
