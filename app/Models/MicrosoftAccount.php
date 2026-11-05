<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrosoftAccount extends Model
{
    use HasFactory;

    protected $table = 'microsoft_accounts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'displayName',
        'mail',
        'userPrincipalName',
    ];

    public function toGraphArray(): array
    {
        return [
            'id' => $this->id,
            'displayName' => $this->displayName,
            'mail' => $this->mail,
            'userPrincipalName' => $this->userPrincipalName,
        ];
    }
}
