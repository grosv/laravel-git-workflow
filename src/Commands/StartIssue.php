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

    /** @var SetBranchForIssue */
    private $branch;

    /** @var ParseGitHubIssues  */
    private $issues;

    /** @var GitCommand  */
    private $git;

    public function __construct(SetBranchForIssue $branch, ParseGitHubIssues $issues, GitCommand $git)
    {
        parent::__construct();
        $this->branch = $branch;
        $this->issues = $issues;
        $this->git = $git;
    }


    public function handle()

    {
        $this->issue = $this->argument('issue');
        if (!preg_match('/_/', $this->issue)) {
            $this->issue = null;
        }

        if (!$this->issue) {
            $open = $this->issues->execute($this->git->execute('gh issue list')->getOutput());

            if (empty($open)) {
                $this->info('There are currently no open issues.');
                return;
            }

            $this->issue = $this->choice('Which issue would you like to work on?', $open, 0);
        }

        $this->info($this->branch->execute($this->issue));

        $this->git->execute('git pull --rebase');


        $this->info('You should be on branch ' . $this->issue . ' (verify with `git status`)');
        $this->info('Use `php artisan issue:update` to update your work throughout the day and `php artisan day:end` at the end of the day.');
    }

}