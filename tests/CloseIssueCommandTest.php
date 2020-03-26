<?php

namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;

class CloseIssueCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function is_creates_a_commit_requesting_a_code_review()
    {
        $this->mock(GetCurrentBranchName::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn('1_feature_branch');
        });

        $this->artisan('issue:close')
            ->expectsOutput('You have requested a code review of 1_feature_branch by @edgrosvenor.')
            ->assertExitCode(0);
    }
}
