<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     required={"id","title","image","body"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Ahmed Mohamed"),
 *     @OA\Property(property="image", type="string", example="ahmed@example.com"),
 *     @OA\Property(property="body", type="string", example="Hello body"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Schemas {}
