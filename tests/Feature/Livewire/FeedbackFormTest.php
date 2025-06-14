<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests\Feature\Livewire;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackForm;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;
use SerhiiKorniienko\LaravelKuchi\Tests\TestCase;

class FeedbackFormTest extends TestCase
{
    #[Test]
    public function it_can_render_feedback_form_component(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->assertStatus(200)
            ->assertSee('💡 SUGGEST FEATURE');
    }

    #[Test]
    public function it_can_toggle_form_visibility(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_can_submit_feedback_successfully(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->assertEquals(0, Feedback::count());

        Livewire::test(FeedbackForm::class)
            ->set('title', 'Test Feature Request')
            ->set('description', 'This is a test feature request description')
            ->set('category', 'feature_request')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertSet('title', '')
            ->assertSet('description', '')
            ->assertDispatched('feedback-submitted');

        $this->assertEquals(1, Feedback::count());

        $feedback = Feedback::first();
        $this->assertEquals('Test Feature Request', $feedback->title);
        $this->assertEquals('This is a test feature request description', $feedback->description);
        $this->assertEquals('feature_request', $feedback->category);
        $this->assertEquals($user->id, $feedback->user_id);
        $this->assertEquals('open', $feedback->status);
        $this->assertEquals(0, $feedback->votes);
        $this->assertNotNull($feedback->metadata);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->call('submit')
            ->assertHasErrors(['title', 'description']);
    }

    #[Test]
    public function it_validates_title_max_length(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->set('title', str_repeat('a', 256))
            ->set('description', 'Valid description')
            ->call('submit')
            ->assertHasErrors(['title']);
    }

    #[Test]
    public function it_validates_description_max_length(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->set('title', 'Valid title')
            ->set('description', str_repeat('a', 2001))
            ->call('submit')
            ->assertHasErrors(['description']);
    }

    #[Test]
    public function it_validates_category_values(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->set('title', 'Valid title')
            ->set('description', 'Valid description')
            ->set('category', 'invalid_category')
            ->call('submit')
            ->assertHasErrors(['category']);
    }

    #[Test]
    public function it_toggles_form_visibility(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->set('title', 'Test Title')
            ->set('description', 'Test Description')
            ->set('category', 'improvement')
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_includes_metadata_in_feedback(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(FeedbackForm::class)
            ->set('title', 'Test Feature')
            ->set('description', 'Test Description')
            ->call('submit');

        $feedback = Feedback::first();
        $this->assertIsArray($feedback->metadata);
        $this->assertArrayHasKey('user_agent', $feedback->metadata);
        $this->assertArrayHasKey('ip_address', $feedback->metadata);
        $this->assertArrayHasKey('submitted_at', $feedback->metadata);
    }
}
