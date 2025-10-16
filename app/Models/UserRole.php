<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'is_primary',
        'permissions',
    ];
    protected $casts = [
        'permissions' => 'json',
        'is_primary' => 'boolean',
    ];
    public function isPrime(): bool
    {
        return config('constants.role.adminId') === $this->id;
    }
    /**
     * Get the users for the user role
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

}
