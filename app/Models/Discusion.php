<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discusion extends Model
{
    use HasFactory;

    protected $table = 'discusions';

    protected $fillable = [
        'description',
        'object_type',
        'object_type_id',
        'user_id',
        'kelas_id',
        'likes',
        'matpel_kode',
    ];

    public function linkedObject(): MorphTo
    {
        return $this->morphTo(null, 'object_type', 'object_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiscusionComment::class);
    }
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function matpel(): BelongsTo
    {
        return $this->belongsTo(Matpel::class, 'matpel_kode', 'kode');
    }
}
