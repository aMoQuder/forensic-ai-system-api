<?php

namespace App\Models;

use App\Http\Controllers\Api\FaceRecogController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidences extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'model_used',
        'data',
        'case_id'
    ];
    protected $casts = [
        'data'  => 'array',
    ];
    public function useCase() {
        return $this->belongsTo( UseCase::class, 'case_id' );
    }

}
