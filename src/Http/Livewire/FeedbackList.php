<?php

namespace SerhiiKorniienko\LaravelKuchi\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;
use SerhiiKorniienko\LaravelKuchi\Models\FeedbackVote;

class FeedbackList extends Component
{
    use WithPagination;

    public string $category = '';

    public string $status = '';

    public string $sortBy = 'latest';

    public string $search = '';

    protected $listeners = ['feedback-submitted' => '$refresh'];

    public function mount(): void
    {
        //
    }

    public function vote($feedbackId, $isUpvote = true): void
    {
        $feedback = Feedback::query()->find($feedbackId);

        if (! $feedback) {
            return;
        }

        $existingVote = $feedback->getUserVote(auth()->id());

        if ($existingVote) {
            if ($existingVote->is_upvote == $isUpvote) {
                // Remove vote if clicking the same vote type
                $existingVote->delete();
                $feedback->decrement('votes', $existingVote->is_upvote ? 1 : -1);
            } else {
                // Change vote type
                $existingVote->update(['is_upvote' => $isUpvote]);
                $feedback->increment('votes', $isUpvote ? 2 : -2);
            }
        } else {
            // New vote
            FeedbackVote::query()->create([
                'user_id' => auth()->id(),
                'feedback_id' => $feedbackId,
                'is_upvote' => $isUpvote,
            ]);
            $feedback->increment('votes', $isUpvote ? 1 : -1);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Feedback::with('user');

        if ($this->search !== '' && $this->search !== '0') {
            $query->where(function ($q): void {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->category !== '' && $this->category !== '0') {
            $query->where('category', $this->category);
        }

        if ($this->status !== '' && $this->status !== '0') {
            $query->where('status', $this->status);
        }

        $query = match ($this->sortBy) {
            'popular' => $query->orderBy('votes', 'desc')->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            // latest
            default => $query->orderBy('created_at', 'desc'),
        };

        $feedback = $query->paginate(config('kuchi.per_page', 10));

        return view('feedback::livewire.feedback-list', [
            'feedback' => $feedback,
            'categories' => config('kuchi.categories', []),
            'statuses' => config('kuchi.statuses', []),
        ]);
    }
}
