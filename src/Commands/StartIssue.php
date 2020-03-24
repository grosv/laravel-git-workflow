<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Grosv\LaravelGitWorkflow\Actions\SetBranchForIssue;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartIssue extends Command
{
    protected $signature = 'issue:start {issue?}';
    public ?string $issue = null;
    public array $branch = [];

    public function handle()
    {
        $this->issue = $this->argument('issue');
        if (!preg_match('/_/', $this->issue)) {
            return;
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

        (new SetBranchForIssue())->execute($this->issue);
        $this->info('You should be on branch ' . $this->issue . ' (verify with `git status`)');
        $this->info('Use `php artisan issue:update` to update your work throughout the day and `php artisan day:end` at the end of the day.');
    }

}