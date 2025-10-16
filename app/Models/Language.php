<?php

namespace App\Models;

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

    public function isPrime(): bool
    {
        return config('constants.language.languageId') === $this->id;
    }

    public function getImage(): string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : asset('images/default/flag.jpg');
    }
}
