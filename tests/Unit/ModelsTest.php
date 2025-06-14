<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use SerhiiKorniienko\LaravelKuchi\Models\BugReport;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;
use SerhiiKorniienko\LaravelKuchi\Models\FeedbackVote;
use SerhiiKorniienko\LaravelKuchi\Tests\TestCase;

class ModelsTest extends TestCase
{
    #[Test]
    public function feedback_model_has_correct_fillable_attributes(): void
    {
        $feedback = new Feedback;

        $expected = [
            'user_id',
            'title',
            'description',
            'category',
            'status',
            'votes',
            'metadata',
        ];

        $this->assertEquals($expected, $feedback->getFillable());
    }

    #[Test]
    public function feedback_model_has_correct_casts(): void
    {
        $feedback = new Feedback;

        $this->assertEquals('array', $feedback->getCasts()['metadata']);
        $this->assertEquals('integer', $feedback->getCasts()['votes']);
    }

    #[Test]
    public function feedback_belongs_to_user(): void
    {
        $user = $this->createUser();
        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test Description',
            'category' => 'feature_request',
        ]);

        $this->assertInstanceOf($user::class, $feedback->user);
        $this->assertEquals($user->id, $feedback->user->id);
    }

    #[Test]
    public function feedback_has_many_feedback_votes(): void
    {
        $user = $this->createUser();
        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test Description',
            'category' => 'feature_request',
        ]);

        FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        $this->assertEquals(1, $feedback->feedbackVotes()->count());
        $this->assertInstanceOf(FeedbackVote::class, $feedback->feedbackVotes()->first());
    }

    #[Test]
    public function feedback_can_check_if_user_voted(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser(['email' => 'other@example.com']);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test Description',
            'category' => 'feature_request',
        ]);

        $this->assertFalse($feedback->hasUserVoted($user->id));

        FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        $this->assertTrue($feedback->hasUserVoted($user->id));
        $this->assertFalse($feedback->hasUserVoted($otherUser->id));
    }

    #[Test]
    public function feedback_can_get_user_vote(): void
    {
        $user = $this->createUser();
        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test Description',
            'category' => 'feature_request',
        ]);

        $this->assertNull($feedback->getUserVote($user->id));

        $vote = FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        $userVote = $feedback->getUserVote($user->id);
        $this->assertInstanceOf(FeedbackVote::class, $userVote);
        $this->assertEquals($vote->id, $userVote->id);
        $this->assertTrue($userVote->is_upvote);
    }

    #[Test]
    public function feedback_category_label_attribute(): void
    {
        $feedback = new Feedback(['category' => 'feature_request']);
        $this->assertEquals('Feature Request', $feedback->category_label);

        $feedback = new Feedback(['category' => 'unknown_category']);
        $this->assertEquals('Unknown category', $feedback->category_label);
    }

    #[Test]
    public function feedback_status_label_attribute(): void
    {
        $feedback = new Feedback(['status' => 'in_progress']);
        $this->assertEquals('In Progress', $feedback->status_label);

        $feedback = new Feedback(['status' => 'unknown_status']);
        $this->assertEquals('Unknown status', $feedback->status_label);
    }

    #[Test]
    public function feedback_scopes_work_correctly(): void
    {
        $user = $this->createUser();

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Feedback',
            'description' => 'Description',
            'category' => 'feature_request',
            'status' => 'open',
            'votes' => 5,
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Feedback',
            'description' => 'Description',
            'category' => 'improvement',
            'status' => 'closed',
            'votes' => 1,
        ]);

        $this->assertEquals(1, Feedback::byStatus('open')->count());
        $this->assertEquals(1, Feedback::byCategory('feature_request')->count());
        $this->assertEquals('Open Feedback', Feedback::popular()->first()->title);
    }

    #[Test]
    public function bug_report_model_has_correct_fillable_attributes(): void
    {
        $bugReport = new BugReport;

        $expected = [
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

        $this->assertEquals($expected, $bugReport->getFillable());
    }

    #[Test]
    public function bug_report_has_correct_casts(): void
    {
        $bugReport = new BugReport;
        $this->assertEquals('array', $bugReport->getCasts()['metadata']);
    }

    #[Test]
    public function bug_report_belongs_to_user(): void
    {
        $user = $this->createUser();
        $bugReport = BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Bug',
            'description' => 'Test Description',
            'priority' => 'medium',
        ]);

        $this->assertInstanceOf($user::class, $bugReport->user);
        $this->assertEquals($user->id, $bugReport->user->id);
    }

    #[Test]
    public function bug_report_priority_label_attribute(): void
    {
        $bugReport = new BugReport(['priority' => 'high']);
        $this->assertEquals('High', $bugReport->priority_label);

        $bugReport = new BugReport(['priority' => 'unknown_priority']);
        $this->assertEquals('Unknown_priority', $bugReport->priority_label);
    }

    #[Test]
    public function bug_report_status_label_attribute(): void
    {
        $bugReport = new BugReport(['status' => 'in_progress']);
        $this->assertEquals('In Progress', $bugReport->status_label);
    }

    #[Test]
    public function bug_report_priority_color_attribute(): void
    {
        $bugReport = new BugReport(['priority' => 'low']);
        $this->assertEquals('text-green-600 bg-green-100', $bugReport->priority_color);

        $bugReport = new BugReport(['priority' => 'critical']);
        $this->assertEquals('text-red-600 bg-red-100', $bugReport->priority_color);
    }

    #[Test]
    public function bug_report_scopes_work_correctly(): void
    {
        $user = $this->createUser();

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Bug',
            'description' => 'Description',
            'priority' => 'critical',
            'status' => 'open',
        ]);

        BugReport::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Bug',
            'description' => 'Description',
            'priority' => 'low',
            'status' => 'closed',
        ]);

        $this->assertEquals(1, BugReport::byStatus('open')->count());
        $this->assertEquals(1, BugReport::byPriority('critical')->count());
        $this->assertEquals(1, BugReport::critical()->count());
    }

    #[Test]
    public function feedback_vote_model_has_correct_fillable_attributes(): void
    {
        $vote = new FeedbackVote;

        $expected = [
            'user_id',
            'feedback_id',
            'is_upvote',
        ];

        $this->assertEquals($expected, $vote->getFillable());
    }

    #[Test]
    public function feedback_vote_has_correct_casts(): void
    {
        $vote = new FeedbackVote;
        $this->assertEquals('boolean', $vote->getCasts()['is_upvote']);
    }

    #[Test]
    public function feedback_vote_belongs_to_user_and_feedback(): void
    {
        $user = $this->createUser();
        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test Description',
            'category' => 'feature_request',
        ]);

        $vote = FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        $this->assertInstanceOf($user::class, $vote->user);
        $this->assertInstanceOf(Feedback::class, $vote->feedback);
        $this->assertEquals($user->id, $vote->user->id);
        $this->assertEquals($feedback->id, $vote->feedback->id);
    }
}
