<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleTask extends Model
{
    use HasFactory;

    protected $table = 'google_tasks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_list_id',
        'title',
        'notes',
        'status',
        'due',
        'completed',
    ];

    public function taskList(): BelongsTo
    {
        return $this->belongsTo(GoogleTaskList::class, 'task_list_id', 'id');
    }

    public function toGoogleArray(): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status ?? 'needsAction',
        ];

        if ($this->notes !== null) {
            $data['notes'] = $this->notes;
        }

        if ($this->due !== null) {
            $data['due'] = $this->due;
        }

        if ($this->completed !== null) {
            $data['completed'] = $this->completed;
        }

        return $data;
    }
}
