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

    public function __construct(GetCurrentBranchName $branch, GitCommand $git)
    {
        parent::__construct();
        $this->branch = $branch->execute();
        $this->git = $git;
    }

    public function handle()
    {
        $this->message = $this->argument('message');

        if ($this->branch === 'master') {
            $this->error('You cannot commit directly to the master branch. Run `php artisan issue:start` to get to your feature branch.');

            return 1;
        }

        if ($this->message) {
            $this->message = $this->ask('In one sentence, what changes have you made since your last commit?');
        }

        $this->git->execute('git add .');
        $this->git->execute('git commit -m "'.$this->message.'"');
        $this->git->execute('git pull --rebase');
        $this->git->execute('git push');

        if ($this->confirm('Are you ready to close this issue and request a review of the pull request?')) {
            $this->call('issue:close '.$this->branch);
        }

        $this->info('Your commit has been added to the pull request.');
    }
}
