<?php

namespace SerhiiKorniienko\LaravelKuchi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string $status
 * @property int $votes
 * @property string[]|null $metadata
 */
class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'votes',
        'metadata',
    ];

    /** @var string[] */
    protected $casts = [
        'metadata' => 'array',
        'votes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function feedbackVotes(): HasMany
    {
        return $this->hasMany(FeedbackVote::class);
    }

    /**
     * @return HasMany<FeedbackVote>
     */
    public function upvotes(): HasMany
    {
        return $this->feedbackVotes()->where('is_upvote', true);
    }

    public function downvotes(): HasMany
    {
        return $this->hasMany(FeedbackVote::class)->where('is_upvote', false);
    }

    public function hasUserVoted($userId): bool
    {
        return $this->feedbackVotes()->where('user_id', $userId)->exists();
    }

    public function getUserVote($userId): ?FeedbackVote
    {
        return $this->feedbackVotes()->where('user_id', $userId)->first();
    }

    public function getCategoryLabelAttribute(): string
    {
        $categories = config('kuchi.categories', []);

        return $categories[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = config('kuchi.statuses', []);

        return $statuses[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('votes', 'desc');
    }
}
