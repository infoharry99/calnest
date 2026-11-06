<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalnestVerificationCode extends Model
{
    use HasFactory;

    protected $table = 'calnest_verification_codes';

    protected $fillable = [
        'email',
        'code',
        'type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
