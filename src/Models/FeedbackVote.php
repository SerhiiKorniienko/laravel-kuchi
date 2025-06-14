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
class FeedbackVote extends Model
{
    protected $fillable = [
        'user_id',
        'feedback_id',
        'is_upvote',
    ];

    /** @var string[] */
    protected $casts = [
        'is_upvote' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }
}
