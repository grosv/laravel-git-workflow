<?php

namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class UpdateIssueCommandTest extends TestCase
{
    private $composer;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('laravel-git-workflow.composer_json', __DIR__.'/test_composer.json');
        Config::set('laravel-git-workflow.repositories', [
            'my-happy-package' => [
                'git'  => 'https://github.com/edgrosvenor/my-happy-package',
                'path' => '../../packages/edgrosvenor/my-happy-package',
            ],
            'my-crazy-package' => [
                'git'  => 'https://github.com/edgrosvenor/my-crazy-package',
                'path' => '../../packages/edgrosvenor/my-crazy-package',
            ],
            'my-sad-package' => [
                'path' => '../../packages/edgrosvenor/my-sad-package',
            ],
        ]);

        $this->composer = File::get(config('laravel-git-workflow.composer_json'));
    }

    public function tearDown(): void
    {
        File::put(config('laravel-git-workflow.composer_json'), $this->composer);
    }

    /** @test */
    public function it_will_not_let_you_commit_directly_to_master()
    {
        $this->mock(GetCurrentBranchName::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn('master');
        });

        $this->artisan('commit', ['message' => 'WIP'])
            ->expectsOutput('You cannot commit directly to the master branch. Run `php artisan issue:start` to get to your feature branch.')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_will_let_you_commit_to_a_feature_branch()
    {
        $this->mock(GetCurrentBranchName::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn('1_feature_branch');
        });

        $this->artisan('commit', ['message' => 'WIP'])
            ->expectsConfirmation('Are you ready to close this issue and request a review of the pull request?')
            ->expectsOutput('Your commit has been added to the pull request.')
            ->assertExitCode(0);

        $updated = File::get(config('laravel-git-workflow.composer_json'));
    }
}
