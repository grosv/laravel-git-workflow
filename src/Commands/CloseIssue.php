<?php

namespace Grosv\LaravelGitWorkflow\Commands;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;
use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Illuminate\Console\Command;

class CloseIssue extends Command
{
    protected $signature = 'issue:close';
    private string $branch;
    private GitCommand $git;
    private string $owner;

    public function __construct(GetCurrentBranchName $branch, GitCommand $git)
    {
        parent::__construct();
        $this->branch = $branch->execute();
        $this->git = $git;
        $this->owner = '@'.config('laravel-git-workflow.project_owner');
    }

    public function handle()
    {
        if ($this->branch === config('laravel-git-workflow.trunk')) {
            $this->error('You cannot request a code review on the main branch. Run `php artisan issue:start` to get on your feature branch.');

            return 1;
        }

        $this->git->execute('git add .');

        $this->git->execute('git commit -m "Requesting+a+code+review+from+'.$this->owner.'" --allow-empty');

        $this->git->execute('git push');

        $this->git->execute('gh pull ready '.$this->branch);

        $this->info('You have requested a code review of '.$this->branch.' by '.$this->owner.'.');
    }
}
