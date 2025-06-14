<?php

namespace SerhiiKorniienko\LaravelKuchi\Http\Livewire;

use Livewire\Component;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;

class FeedbackForm extends Component
{
    public string $title = '';

    public string $description = '';

    public string $category = 'feature_request';

    public bool $showForm = false;

    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2000',
        'category' => 'required|in:feature_request,improvement,question,other',
    ];

    public function mount(): void
    {
        $this->category = 'feature_request';
    }

    public function submit(): void
    {
        $this->validate();

        Feedback::query()->create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'metadata' => [
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'submitted_at' => now()->toISOString(),
            ],
        ]);

        $this->reset(['title', 'description', 'category']);
        $this->showForm = false;

        session()->flash('message', 'Thank you for your feedback! We appreciate your input.');

        $this->dispatch('feedback-submitted');
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->title = '';
            $this->description = '';
            $this->category = 'feature_request';
        }
    }

    public function render()
    {
        return view('feedback::livewire.feedback-form', [
            'categories' => config('kuchi.categories', []),
        ]);
    }
}
