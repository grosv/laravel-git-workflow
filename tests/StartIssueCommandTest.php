<?php


namespace Tests;


use Grosv\LaravelGitWorkflow\Actions\SetBranchForIssue;

class StartIssueCommandTest extends TestCase
{
    /** @test */
    public function it_can_start_new_issue_pass_to_command()
    {
        $this->mock(SetBranchForIssue::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->with('1_this_is_an_issue')
                ->once()
                ->andReturn('Created Branches and Draft PR for 1_this_is_an_issue');
        });

        $this->artisan('issue:start', ['issue' => '1_this_is_an_issue'])
            ->expectsOutput('Created Branches and Draft PR for 1_this_is_an_issue')
            ->expectsOutput('You should be on branch 1_this_is_an_issue (verify with `git status`)')
            ->expectsOutput('Use `php artisan issue:update` to update your work throughout the day and `php artisan day:end` at the end of the day.')
            ->assertExitCode(0);


    }

    /** @test */
    public function it_can_continue_issue_in_progress()
    {
        $this->mock(SetBranchForIssue::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andSet('branches', ['1_this_is_an_issue'])
                ->with('1_this_is_an_issue')
                ->once()
                ->andReturn('Checked Out Existing Branch 1_this_is_an_issue');
        });

        $this->artisan('issue:start', ['issue' => '1_this_is_an_issue'])
            ->expectsOutput('Checked Out Existing Branch 1_this_is_an_issue')
            ->expectsOutput('You should be on branch 1_this_is_an_issue (verify with `git status`)')
            ->expectsOutput('Use `php artisan issue:update` to update your work throughout the day and `php artisan day:end` at the end of the day.')
            ->assertExitCode(0);
    }
}