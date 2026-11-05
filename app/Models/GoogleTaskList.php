<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleTaskList extends Model
{
    use HasFactory;

    protected $table = 'google_task_lists';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'updated_at_iso',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(GoogleTask::class, 'task_list_id', 'id');
    }

    public function toGoogleArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'updated' => $this->updated_at_iso ?? $this->updated_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
