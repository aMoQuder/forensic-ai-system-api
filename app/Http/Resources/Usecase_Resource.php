<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Usecase_Resource extends JsonResource {
    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'status'=>$this->status,
            'user_id'=>$this->user_id,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
