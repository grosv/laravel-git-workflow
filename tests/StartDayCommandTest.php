<?php


namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class StartDayCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

    }
    /** @test */
    public function it_prompts_for_github_username_if_not_in_env()
    {
        $before = File::get(__DIR__ . '/.env.before');
        $after = File::get(__DIR__.'/.env.after');
        $env = File::get(__DIR__ . '/.env');

        $this->assertEquals($before, $env);

        $this->artisan('day:start')
            ->expectsQuestion('What is your GitHub username? (https://github.com/your_username)', 'edgrosvenor')
            ->assertExitCode(0);

        $env = File::get(__DIR__ . '/.env');
        $this->assertEquals($after, $env);
    }

    /** @test */
    public function it_checks_for_requirements()
    {
        Config::set('laravel-git-workflow.github_user', 'edgrosvenor');
        $this->mock(ParseGitHubIssues::class, function ($mock) {
            $mock->shouldReceive('execute')->once()
                ->andReturn([]);
        });

        $this->artisan('day:start')
            ->expectsOutput('✔️ GitHub CLI appears to be installed')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_no_issues()
    {
        Config::set('laravel-git-workflow.github_user', 'edgrosvenor');
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
        Config::set('laravel-git-workflow.github_user', 'edgrosvenor');
        $this->mock(ParseGitHubIssues::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn([['number' => '1', 'title' => 'Something Is Broken']]);
        });

        $this->artisan('day:start')
            ->expectsQuestion('Which issue would you like to work on?', 'None Right Now')
            ->assertExitCode(0);
    }
}