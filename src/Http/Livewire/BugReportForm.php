<?php

namespace SerhiiKorniienko\LaravelKuchi\Http\Livewire;

use Livewire\Component;
use SerhiiKorniienko\LaravelKuchi\Models\BugReport;

class BugReportForm extends Component
{
    public string $title = '';

    public string $description = '';

    public string $steps_to_reproduce = '';

    public string $expected_behavior = '';

    public string $actual_behavior = '';

    public string $priority = 'medium';

    public string $browser = '';

    public string $operating_system = '';

    public string $url = '';

    public bool $showForm = false;

    /** @var array|string[] */
    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2000',
        'steps_to_reproduce' => 'nullable|string|max:2000',
        'expected_behavior' => 'nullable|string|max:1000',
        'actual_behavior' => 'nullable|string|max:1000',
        'priority' => 'required|in:low,medium,high,critical',
        'browser' => 'nullable|string|max:100',
        'operating_system' => 'nullable|string|max:100',
        'url' => 'nullable|url|max:500',
    ];

    public function mount(): void
    {
        $this->priority = 'medium';
        $this->url = request()->url();
        $this->detectBrowserAndOS();
    }

    public function submit(): void
    {
        $this->validate();

        BugReport::query()->create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'steps_to_reproduce' => $this->steps_to_reproduce,
            'expected_behavior' => $this->expected_behavior,
            'actual_behavior' => $this->actual_behavior,
            'priority' => $this->priority,
            'browser' => $this->browser,
            'operating_system' => $this->operating_system,
            'url' => $this->url,
            'metadata' => [
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'submitted_at' => now()->toISOString(),
            ],
        ]);

        $this->reset([
            'title', 'description', 'steps_to_reproduce',
            'expected_behavior', 'actual_behavior', 'priority',
        ]);
        $this->showForm = false;

        session()->flash('message', 'Bug report submitted successfully! We\'ll investigate this issue.');

        $this->dispatch('bug-report-submitted');
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->title = '';
            $this->description = '';
            $this->steps_to_reproduce = '';
            $this->expected_behavior = '';
            $this->actual_behavior = '';
            $this->priority = 'medium';
        }
    }

    private function detectBrowserAndOS(): void
    {
        $userAgent = request()->userAgent();

        // Simple browser detection
        if (str_contains((string) $userAgent, 'Chrome')) {
            $this->browser = 'Chrome';
        } elseif (str_contains((string) $userAgent, 'Firefox')) {
            $this->browser = 'Firefox';
        } elseif (str_contains((string) $userAgent, 'Safari')) {
            $this->browser = 'Safari';
        } elseif (str_contains((string) $userAgent, 'Edge')) {
            $this->browser = 'Edge';
        }

        // Simple OS detection
        if (str_contains((string) $userAgent, 'Windows')) {
            $this->operating_system = 'Windows';
        } elseif (str_contains((string) $userAgent, 'Mac')) {
            $this->operating_system = 'macOS';
        } elseif (str_contains((string) $userAgent, 'Linux')) {
            $this->operating_system = 'Linux';
        } elseif (str_contains((string) $userAgent, 'Android')) {
            $this->operating_system = 'Android';
        } elseif (str_contains((string) $userAgent, 'iOS')) {
            $this->operating_system = 'iOS';
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('feedback::livewire.bug-report-form', [
            'priorities' => config('kuchi.bug_priorities', []),
        ]);
    }
}
