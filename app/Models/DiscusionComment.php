<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscusionComment extends Model
{
    /** @use HasFactory<\Database\Factories\DiscusionCommentFactory> */
    use HasFactory;


    protected $fillable = [
        'discusion_id',
        'user_id',
        'text',
    ];


    public function discusion(): BelongsTo
    {
        return $this->belongsTo(Discusion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
