<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'image' => $this->image
            ? asset('storage/' . $this->image)
            : null,            'email'=>$this->email,
            'role'=>$this->role,
            'national_id'=>$this->national_id,
            'phone_number'=>$this->phone_number,
            'date_of_birth'=>$this->date_of_birth,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
