<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class QueueManagement extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'key',
        'status',
        'location_id',
        'authentication_code',
        'authentication_code_status',
        'url',
    ];



    protected $casts = [
        'status' => 'boolean',
        'authentication_code_status' => 'boolean',
        'url_status' => 'boolean'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
