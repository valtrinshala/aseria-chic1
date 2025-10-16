<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AseriaManagement extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'image',
        'second_image',
        'company_name',
        'website',
        'working_hours',
        'working_days',
        'telephone',
        'urgent_calls',
        'email',
        'description'
    ];
}
