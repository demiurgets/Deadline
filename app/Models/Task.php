<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'due_date',
        'user_id', 
        'note',    
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
