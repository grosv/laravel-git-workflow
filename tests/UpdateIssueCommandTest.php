<?php

namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;

class UpdateIssueCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
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
            ->expectsQuestion('In one sentence, what changes have you made since your last commit?', 'WIP')
            ->expectsConfirmation('Are you ready to close this issue and request a review of the pull request?')
            ->expectsOutput('Your commit has been added to the pull request.')
            ->assertExitCode(0);
    }
}
