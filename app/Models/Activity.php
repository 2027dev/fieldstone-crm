<?php

namespace App\Models;

use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'due_date',
        'done',
        'priority',
        'outcome',
        'contact_id',
        'deal_id',
        'owner_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'done' => 'boolean',
    ];

    public const TYPES = [
        'call' => 'Call',
        'meeting' => 'Meeting',
        'task' => 'Task',
        'deadline' => 'Deadline',
        'email' => 'Email',
        'lunch' => 'Lunch',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
