<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'collaborative_mode',
        'ainl_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a new User model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

    }


    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'user_collaborators', 'user_id', 'collaborator_id')
            ->withTimestamps();
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->categories()->attach([
                1, // ID of 'work' category
                2, // ID of 'school' category
                3, // ID of 'personal' category
            ]);
        });
    }

    public function categories()
{
    return $this->hasMany(Category::class);
}

}
