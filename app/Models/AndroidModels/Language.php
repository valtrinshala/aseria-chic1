<?php

namespace App\Models\AndroidModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class Language extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'locale',
        'set2',
        'image',
        'direction'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * User avatar url
     *
     * @return string
     */
    public function getImageAttribute($image): string
    {
        return $image
            ? Storage::disk('public')->url($image)
            : asset('images/default/flag.jpg');
    }

    public function isPrime(): bool
    {
        return "d219fea9-1d25-4d1a-a7fa-a9d44c4cd5e2" === $this->id;
    }
}
