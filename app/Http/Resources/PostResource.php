<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource {
public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'content' => $this->content,
        'type' => $this->type,
        'image' => $this->image
            ? asset('storage/' . $this->image)
            : null,
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
                    'image' => $comment->user->image
                        ? asset('storage/' . $comment->user->image)
                        : null,
                ],

                'created_at' => $comment->created_at->diffForHumans(),
            ];
        }),
        'is_liked' => (bool) $this->is_liked,

        'created_at' => $this->created_at->diffForHumans(),
    ];
}
}
