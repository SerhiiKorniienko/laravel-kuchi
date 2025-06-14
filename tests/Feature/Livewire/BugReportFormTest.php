<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests\Feature\Livewire;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\BugReportForm;
use SerhiiKorniienko\LaravelKuchi\Models\BugReport;
use SerhiiKorniienko\LaravelKuchi\Tests\TestCase;

class BugReportFormTest extends TestCase
{
    #[Test]
    public function it_can_render_bug_report_form_component(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->assertStatus(200)
            ->assertSee('🐛 REPORT BUG');
    }

    #[Test]
    public function it_can_toggle_form_visibility(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_can_submit_bug_report_successfully(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->assertEquals(0, BugReport::count());

        Livewire::test(BugReportForm::class)
            ->set('title', 'Test Bug Report')
            ->set('description', 'This is a test bug description')
            ->set('steps_to_reproduce', '1. Do this\n2. Then this\n3. Bug appears')
            ->set('expected_behavior', 'Should work correctly')
            ->set('actual_behavior', 'Throws an error')
            ->set('priority', 'high')
            ->set('browser', 'Chrome')
            ->set('operating_system', 'Windows')
            ->set('url', 'https://example.com/bug-page')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertDispatched('bug-report-submitted');

        $this->assertEquals(1, BugReport::count());

        $bugReport = BugReport::query()->first();
        $this->assertEquals('Test Bug Report', $bugReport->title);
        $this->assertEquals('This is a test bug description', $bugReport->description);
        $this->assertEquals('1. Do this\n2. Then this\n3. Bug appears', $bugReport->steps_to_reproduce);
        $this->assertEquals('Should work correctly', $bugReport->expected_behavior);
        $this->assertEquals('Throws an error', $bugReport->actual_behavior);
        $this->assertEquals('high', $bugReport->priority);
        $this->assertEquals('Chrome', $bugReport->browser);
        $this->assertEquals('Windows', $bugReport->operating_system);
        $this->assertEquals('https://example.com/bug-page', $bugReport->url);
        $this->assertEquals($user->id, $bugReport->user_id);
        $this->assertEquals('open', $bugReport->status);
        $this->assertNotNull($bugReport->metadata);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->call('submit')
            ->assertHasErrors(['title', 'description']);
    }

    #[Test]
    public function it_validates_title_max_length(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
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

        Livewire::test(BugReportForm::class)
            ->set('title', 'Valid title')
            ->set('description', str_repeat('a', 2001))
            ->call('submit')
            ->assertHasErrors(['description']);
    }

    #[Test]
    public function it_validates_priority_values(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->set('title', 'Valid title')
            ->set('description', 'Valid description')
            ->set('priority', 'invalid_priority')
            ->call('submit')
            ->assertHasErrors(['priority']);
    }

    #[Test]
    public function it_validates_url_format(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->set('title', 'Valid title')
            ->set('description', 'Valid description')
            ->set('url', 'not-a-valid-url')
            ->call('submit')
            ->assertHasErrors(['url']);
    }

    #[Test]
    public function it_sets_default_priority_to_medium(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->assertSet('priority', 'medium');
    }

    #[Test]
    public function it_sets_current_url_by_default(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->assertSet('url', request()->url());
    }

    #[Test]
    public function it_toggles_form_visibility(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->set('title', 'Test Title')
            ->set('description', 'Test Description')
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_includes_metadata_in_bug_report(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(BugReportForm::class)
            ->set('title', 'Test Bug')
            ->set('description', 'Test Description')
            ->call('submit');

        $bugReport = BugReport::first();
        $this->assertIsArray($bugReport->metadata);
        $this->assertArrayHasKey('user_agent', $bugReport->metadata);
        $this->assertArrayHasKey('ip_address', $bugReport->metadata);
        $this->assertArrayHasKey('submitted_at', $bugReport->metadata);
    }
}
