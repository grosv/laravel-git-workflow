<?php

namespace Grosv\LaravelGitWorkflow\Commands;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;
use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Illuminate\Console\Command;

class UpdateIssue extends Command
{
    protected $signature = 'commit {message?}';
    public ?string $branch;
    public ?string $message;
    private GitCommand $git;
    private array $repos;

    public function __construct(GetCurrentBranchName $branch, GitCommand $git)
    {
        parent::__construct();
        $this->branch = $branch->execute();
        $this->git = $git;
        $this->repos = config('laravel-git-workflow.repositories') ?? [];
    }

    public function handle()
    {
        $this->message = $this->argument('message');

        if ($this->branch === config('laravel-git-workflow.trunk')) {
            $this->error('You cannot commit directly to the '.config('laravel-git-workflow.trunk').' branch. Run `php artisan issue:start` to get to your feature branch.');

            return 1;
        }

        if (strlen($this->message) < 1) {
            $this->message = $this->ask('Please enter a commit message:', config('laravel-git-workflow.wip'));
        }

        $this->git->execute('git add .');
        $this->git->execute('git commit -m "'.$this->message.'"');
        $this->git->execute('git pull --rebase');
        $this->git->execute('git push');

        if ($this->confirm('Are you ready to close this issue and request a review of the pull request?')) {
            $this->call('issue:close');
        }

        $this->info('Your commit has been added to the pull request.');

        return 0;
    }
}
