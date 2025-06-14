<?php

namespace SerhiiKorniienko\LaravelKuchi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $steps_to_reproduce
 * @property string $expected_behavior
 * @property string $actual_behavior
 * @property string $priority
 * @property string $status
 * @property string $browser
 * @property string $operating_system
 * @property string|null $url
 * @property string[]|null $metadata
 */
class BugReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'steps_to_reproduce',
        'expected_behavior',
        'actual_behavior',
        'priority',
        'status',
        'browser',
        'operating_system',
        'url',
        'metadata',
    ];

    /** @var string[] */
    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function getPriorityLabelAttribute(): string
    {
        $priorities = config('kuchi.bug_priorities', []);

        return $priorities[$this->priority] ?? ucfirst($this->priority);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = config('kuchi.statuses', []);

        return $statuses[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'high' => 'text-orange-600 bg-orange-100',
            'critical' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }
}
