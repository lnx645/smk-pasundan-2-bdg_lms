<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanTugas extends Model
{
    /** @use HasFactory<\Database\Factories\JawabanTugasFactory> */
    protected $primaryKey = 'jawabanID';
    use HasFactory, HasUuids;

    public function user()
    {
        return $this->belongsTo(User::class, 'answered_by_id');
    }
}
