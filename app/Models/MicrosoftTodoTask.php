<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrosoftTodoTask extends Model
{
    use HasFactory;

    protected $table = 'microsoft_todo_tasks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'list_id',
        'title',
        'status',
        'body_content',
        'body_contentType',
        'due_dateTime',
        'due_timeZone',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(MicrosoftTodoList::class, 'list_id', 'id');
    }

    public function toGraphArray(): array
    {
        $res = [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status ?? 'notStarted',
            'body' => [
                'contentType' => $this->body_contentType ?? 'text',
                'content' => $this->body_content ?? '',
            ],
        ];

        if ($this->due_dateTime) {
            $res['dueDateTime'] = [
                'dateTime' => $this->due_dateTime,
                'timeZone' => $this->due_timeZone ?? 'UTC',
            ];
        }

        return $res;
    }
}
