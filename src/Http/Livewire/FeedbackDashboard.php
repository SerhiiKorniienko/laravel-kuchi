<?php

namespace SerhiiKorniienko\LaravelKuchi\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use SerhiiKorniienko\LaravelKuchi\Models\BugReport;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;

class FeedbackDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'feedback';

    public string $feedbackStatus = '';

    public string $bugStatus = '';

    public string $feedbackCategory = '';

    public string $bugPriority = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updateFeedbackStatus($id, $status): void
    {
        $feedback = Feedback::query()->find($id);
        if ($feedback) {
            $feedback->update(['status' => $status]);
            session()->flash('message', 'Feedback status updated successfully.');
        }
    }

    public function updateBugStatus($id, $status): void
    {
        $bug = BugReport::query()->find($id);
        if ($bug) {
            $bug->update(['status' => $status]);
            session()->flash('message', 'Bug report status updated successfully.');
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $feedbackQuery = Feedback::with('user')->latest();
        $bugQuery = BugReport::with('user')->latest();

        if ($this->feedbackStatus !== '' && $this->feedbackStatus !== '0') {
            $feedbackQuery->where('status', $this->feedbackStatus);
        }

        if ($this->feedbackCategory !== '' && $this->feedbackCategory !== '0') {
            $feedbackQuery->where('category', $this->feedbackCategory);
        }

        if ($this->bugStatus !== '' && $this->bugStatus !== '0') {
            $bugQuery->where('status', $this->bugStatus);
        }

        if ($this->bugPriority !== '' && $this->bugPriority !== '0') {
            $bugQuery->where('priority', $this->bugPriority);
        }

        $feedback = $feedbackQuery->paginate(15, ['*'], 'feedback_page');
        $bugs = $bugQuery->paginate(15, ['*'], 'bugs_page');

        $stats = [
            'total_feedback' => Feedback::query()->count(),
            'open_feedback' => Feedback::query()->where('status', 'open')->count(),
            'total_bugs' => BugReport::query()->count(),
            'open_bugs' => BugReport::query()->where('status', 'open')->count(),
            'critical_bugs' => BugReport::query()->where('priority', 'critical')->where('status', '!=', 'closed')->count(),
        ];

        return view('feedback::livewire.feedback-dashboard', [
            'feedback' => $feedback,
            'bugs' => $bugs,
            'stats' => $stats,
            'categories' => config('kuchi.categories', []),
            'statuses' => config('kuchi.statuses', []),
            'priorities' => config('kuchi.bug_priorities', []),
        ]);
    }
}
