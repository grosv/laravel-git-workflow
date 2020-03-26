<?php

namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\RunTests;

class EndDayCommandTest extends TestCase
{
    public function __setUp(): void
    {
        parent::__setUp();
    }

    /** @test */
    public function it_prompts_for_confirmation_if_tests_fail()
    {
        $this->mock(RunTests::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn(1);
        });
        $this->artisan('day:end')
            ->expectsConfirmation('Your tests are not currently passing. Are you sure you want to quit?', 'yes')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_prompts_for_the_number_of_hours()
    {
        $this->mock(RunTests::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn(0);
        });

        $this->artisan('day:end')
            ->expectsQuestion('How many hours of work did you do during this session?', 4)
            ->assertExitCode(0);
    }
}
