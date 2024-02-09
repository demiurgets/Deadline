<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'due_date',
        'note',    
    ];

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }
}
