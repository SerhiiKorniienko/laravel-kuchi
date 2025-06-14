<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests\Feature\Livewire;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackDashboard;
use SerhiiKorniienko\LaravelKuchi\Models\BugReport;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;
use SerhiiKorniienko\LaravelKuchi\Tests\TestCase;

class FeedbackDashboardTest extends TestCase
{
    #[Test]
    public function it_can_render_feedback_dashboard_component(): void
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        Livewire::test(FeedbackDashboard::class)
            ->assertStatus(200)
            ->assertSee('IDEAS')
            ->assertSee('BUGS');
    }

    #[Test]
    public function it_displays_correct_statistics(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Create test data
        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Feedback',
            'description' => 'Description',
            'category' => 'feature_request',
            'status' => 'open',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Feedback',
            'description' => 'Description',
            'category' => 'improvement',
            'status' => 'closed',
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Bug',
            'description' => 'Description',
            'priority' => 'high',
            'status' => 'open',
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Critical Bug',
            'description' => 'Description',
            'priority' => 'critical',
            'status' => 'in_progress',
        ]);

        $component = Livewire::test(FeedbackDashboard::class);
        $stats = $component->viewData('stats');

        $this->assertEquals(2, $stats['total_feedback']);
        $this->assertEquals(1, $stats['open_feedback']);
        $this->assertEquals(2, $stats['total_bugs']);
        $this->assertEquals(1, $stats['open_bugs']);
        $this->assertEquals(1, $stats['critical_bugs']);
    }

    #[Test]
    public function it_can_switch_between_tabs(): void
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        Livewire::test(FeedbackDashboard::class)
            ->assertSet('activeTab', 'feedback')
            ->call('setTab', 'bugs')
            ->assertSet('activeTab', 'bugs')
            ->call('setTab', 'feedback')
            ->assertSet('activeTab', 'feedback');
    }

    #[Test]
    public function it_can_filter_feedback_by_status(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Feedback',
            'description' => 'Description',
            'category' => 'feature_request',
            'status' => 'open',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Feedback',
            'description' => 'Description',
            'category' => 'feature_request',
            'status' => 'closed',
        ]);

        $component = Livewire::test(FeedbackDashboard::class)
            ->set('feedbackStatus', 'open');

        $feedback = $component->viewData('feedback');
        $this->assertEquals(1, $feedback->count());
        $this->assertEquals('Open Feedback', $feedback->first()->title);
    }

    #[Test]
    public function it_can_filter_feedback_by_category(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Feature Request',
            'description' => 'Description',
            'category' => 'feature_request',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Improvement',
            'description' => 'Description',
            'category' => 'improvement',
        ]);

        $component = Livewire::test(FeedbackDashboard::class)
            ->set('feedbackCategory', 'feature_request');

        $feedback = $component->viewData('feedback');
        $this->assertEquals(1, $feedback->count());
        $this->assertEquals('Feature Request', $feedback->first()->title);
    }

    #[Test]
    public function it_can_filter_bugs_by_status(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Bug',
            'description' => 'Description',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Bug',
            'description' => 'Description',
            'priority' => 'medium',
            'status' => 'closed',
        ]);

        $component = Livewire::test(FeedbackDashboard::class)
            ->set('activeTab', 'bugs')
            ->set('bugStatus', 'open');

        $bugs = $component->viewData('bugs');
        $this->assertEquals(1, $bugs->count());
        $this->assertEquals('Open Bug', $bugs->first()->title);
    }

    #[Test]
    public function it_can_filter_bugs_by_priority(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'High Priority Bug',
            'description' => 'Description',
            'priority' => 'high',
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Low Priority Bug',
            'description' => 'Description',
            'priority' => 'low',
        ]);

        $component = Livewire::test(FeedbackDashboard::class)
            ->set('activeTab', 'bugs')
            ->set('bugPriority', 'high');

        $bugs = $component->viewData('bugs');
        $this->assertEquals(1, $bugs->count());
        $this->assertEquals('High Priority Bug', $bugs->first()->title);
    }

    #[Test]
    public function it_can_update_feedback_status(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Description',
            'category' => 'feature_request',
            'status' => 'open',
        ]);

        Livewire::test(FeedbackDashboard::class)
            ->call('updateFeedbackStatus', $feedback->id, 'in_progress');

        $feedback->refresh();
        $this->assertEquals('in_progress', $feedback->status);
    }

    #[Test]
    public function it_can_update_bug_status(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $bug = BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Bug',
            'description' => 'Description',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        Livewire::test(FeedbackDashboard::class)
            ->call('updateBugStatus', $bug->id, 'resolved');

        $bug->refresh();
        $this->assertEquals('resolved', $bug->status);
    }

    #[Test]
    public function it_displays_feedback_and_bug_data(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test feedback description',
            'category' => 'feature_request',
            'votes' => 5,
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Bug',
            'description' => 'Test bug description',
            'priority' => 'high',
            'url' => 'https://example.com',
        ]);

        Livewire::test(FeedbackDashboard::class)
            ->assertSee('Test Feedback')
            ->assertSee('Test feedback description')
            ->assertSee('5 VOTES')
            ->set('activeTab', 'bugs')
            ->assertSee('Test Bug')
            ->assertSee('Test bug description')
            ->assertSee('https://example.com');
    }
}
