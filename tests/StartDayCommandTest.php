<?php


namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;

class StartDayCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function it_checks_for_requirements()
    {
        $this->mock(ParseGitHubIssues::class, function ($mock) {
            $mock->shouldReceive('execute')->once()
                ->andReturn([]);
        });

        $this->artisan('day:start')
            ->expectsOutput('✔️ The Symfony process runner seems to be working')
            ->expectsOutput('✔️ GitHub CLI appears to be installed')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_no_issues()
    {

        $this->mock(ParseGitHubIssues::class, function ($mock) {
            $mock->shouldReceive('execute')->once()
                ->andReturn([]);
        });

        $this->artisan('day:start')
            ->expectsOutput('✔️ There are no open issues at this time')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_open_issues()
    {

        $this->mock(ParseGitHubIssues::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn([['number' => '1', 'title' => 'Something Is Broken']]);
        });

        $this->artisan('day:start')
            ->expectsQuestion('Which issue would you like to work on?', 'None Right Now')
            ->assertExitCode(0);
    }
}