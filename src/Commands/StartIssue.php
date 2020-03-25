<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\ParseGitBranches;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Grosv\LaravelGitWorkflow\Actions\SetBranchForIssue;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartIssue extends Command
{
    protected $signature = 'issue:start {issue?}';
    public ?string $issue = null;
    public array $branch = [];


    public function handle(SetBranchForIssue $branch)

    {
        $this->issue = $this->argument('issue');
        if (!preg_match('/_/', $this->issue)) {
            $this->issue = null;
        }

        if (!$this->issue) {
            $process = new Process(['gh', 'issue', 'list']);
            $process->run();
            $open = (new ParseGitHubIssues())->execute($process->getOutput());

            if (empty($open)) {
                $this->info('There are currently no open issues.');
                return;
            }

            $this->issue = $this->choice('Which issue would you like to work on?', $open, 0);
        }

        $branch->execute($this->issue);


        $this->info('You should be on branch ' . $this->issue . ' (verify with `git status`)');
        $this->info('Use `php artisan issue:update` to update your work throughout the day and `php artisan day:end` at the end of the day.');
    }

}