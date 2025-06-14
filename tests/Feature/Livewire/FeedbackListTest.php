<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests\Feature\Livewire;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackList;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;
use SerhiiKorniienko\LaravelKuchi\Models\FeedbackVote;
use SerhiiKorniienko\LaravelKuchi\Tests\TestCase;

class FeedbackListTest extends TestCase
{
    #[Test]
    public function it_can_render_feedback_list_component(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackList::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_displays_feedback_items(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'This is a test feedback',
            'category' => 'feature_request',
            'status' => 'open',
            'votes' => 5,
        ]);

        Livewire::test(FeedbackList::class)
            ->assertSee('Test Feedback')
            ->assertSee('This is a test feedback')
            ->assertSee('5');
    }

    #[Test]
    public function it_can_search_feedback(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Searchable Feedback',
            'description' => 'This feedback should be found',
            'category' => 'feature_request',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Other Feedback',
            'description' => 'This should not be found',
            'category' => 'improvement',
        ]);

        Livewire::test(FeedbackList::class)
            ->set('search', 'Searchable')
            ->assertSee('Searchable Feedback')
            ->assertDontSee('Other Feedback');
    }

    #[Test]
    public function it_can_filter_by_category(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Feature Request',
            'description' => 'Feature description',
            'category' => 'feature_request',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Improvement',
            'description' => 'Improvement description',
            'category' => 'improvement',
        ]);

        Livewire::test(FeedbackList::class)
            ->set('category', 'feature_request')
            ->assertSee('Feature Request')
            ->assertDontSee('Improvement');
    }

    #[Test]
    public function it_can_filter_by_status(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Open Feedback',
            'description' => 'Open description',
            'category' => 'feature_request',
            'status' => 'open',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Closed Feedback',
            'description' => 'Closed description',
            'category' => 'feature_request',
            'status' => 'closed',
        ]);

        Livewire::test(FeedbackList::class)
            ->set('status', 'open')
            ->assertSee('Open Feedback')
            ->assertDontSee('Closed Feedback');
    }

    #[Test]
    public function it_can_sort_by_latest(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Clear any existing feedback to ensure clean test state
        Feedback::query()->delete();

        // Create old feedback with explicit timestamp
        $oldDate = now()->subDays(2);
        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Old Feedback',
            'description' => 'Old description',
            'category' => 'feature_request',
            'created_at' => $oldDate,
        ]);

        // Force a different timestamp by adding a second
        sleep(1);

        // Create new feedback with current timestamp
        $newDate = now();
        Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'New Feedback',
            'description' => 'New description',
            'category' => 'feature_request',
            'created_at' => $newDate,
        ]);

        // Skip the timestamp assertion and just check the sorting result
        $component = Livewire::test(FeedbackList::class)
            ->set('sortBy', 'latest');

        $feedback = $component->viewData('feedback');
        $this->assertEquals('New Feedback', $feedback->first()->title);
    }

    #[Test]
    public function it_can_sort_by_popular(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Clear any existing feedback to ensure clean test state
        Feedback::query()->delete();

        // Create low votes feedback first
        $lowVotes = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Low Votes',
            'description' => 'Low description',
            'category' => 'feature_request',
            'votes' => 1,
        ]);

        // Create high votes feedback second
        $highVotes = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'High Votes',
            'description' => 'High description',
            'category' => 'feature_request',
            'votes' => 10,
        ]);

        // Ensure votes are different
        $this->assertNotEquals(
            $lowVotes->votes,
            $highVotes->votes,
            'Test requires different vote counts for proper sorting'
        );

        $component = Livewire::test(FeedbackList::class)
            ->set('sortBy', 'popular');

        $feedback = $component->viewData('feedback');
        $this->assertEquals('High Votes', $feedback->first()->title);
    }

    #[Test]
    public function it_can_upvote_feedback(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test description',
            'category' => 'feature_request',
            'votes' => 0,
        ]);

        Livewire::test(FeedbackList::class)
            ->call('vote', $feedback->id, true);

        $feedback->refresh();
        $this->assertEquals(1, $feedback->votes);
        $this->assertEquals(1, FeedbackVote::count());

        $vote = FeedbackVote::first();
        $this->assertTrue($vote->is_upvote);
        $this->assertEquals($user->id, $vote->user_id);
        $this->assertEquals($feedback->id, $vote->feedback_id);
    }

    #[Test]
    public function it_can_downvote_feedback(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test description',
            'category' => 'feature_request',
            'votes' => 0,
        ]);

        Livewire::test(FeedbackList::class)
            ->call('vote', $feedback->id, false);

        $feedback->refresh();
        $this->assertEquals(-1, $feedback->votes);
        $this->assertEquals(1, FeedbackVote::count());

        $vote = FeedbackVote::first();
        $this->assertFalse($vote->is_upvote);
    }

    #[Test]
    public function it_can_change_vote_type(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test description',
            'category' => 'feature_request',
            'votes' => 1,
        ]);

        // Create initial upvote
        FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        // Change to downvote
        Livewire::test(FeedbackList::class)
            ->call('vote', $feedback->id, false);

        $feedback->refresh();
        $this->assertEquals(-1, $feedback->votes); // 1 - 2 = -1 (removed upvote and added downvote)
        $this->assertEquals(1, FeedbackVote::count());

        $vote = FeedbackVote::first();
        $this->assertFalse($vote->is_upvote);
    }

    #[Test]
    public function it_can_remove_vote_by_clicking_same_vote_type(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $feedback = Feedback::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Feedback',
            'description' => 'Test description',
            'category' => 'feature_request',
            'votes' => 1,
        ]);

        // Create initial upvote
        FeedbackVote::query()->create([
            'user_id' => $user->id,
            'feedback_id' => $feedback->id,
            'is_upvote' => true,
        ]);

        // Click upvote again to remove
        Livewire::test(FeedbackList::class)
            ->call('vote', $feedback->id, true);

        $feedback->refresh();
        $this->assertEquals(0, $feedback->votes);
        $this->assertEquals(0, FeedbackVote::count());
    }

    #[Test]
    public function it_shows_no_feedback_message_when_empty(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackList::class)
            ->assertSee('NO IDEAS YET!')
            ->assertSee('BE THE FIRST TO SHARE!');
    }
}
