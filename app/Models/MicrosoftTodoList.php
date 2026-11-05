<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicrosoftTodoList extends Model
{
    use HasFactory;

    protected $table = 'microsoft_todo_lists';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'displayName',
        'isOwner',
        'wellknownListName',
    ];

    protected $casts = [
        'isOwner' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(MicrosoftTodoTask::class, 'list_id', 'id');
    }

    public function toGraphArray(): array
    {
        return [
            'id' => $this->id,
            'displayName' => $this->displayName,
            'isOwner' => (bool)$this->isOwner,
            'wellknownListName' => $this->wellknownListName ?? 'defaultList',
        ];
    }
}
