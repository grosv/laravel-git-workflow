<?php

namespace Grosv\LaravelGitWorkflow\Commands;

use Grosv\LaravelGitWorkflow\Actions\GetCurrentBranchName;
use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\RunTests;
use Illuminate\Console\Command;

class EndDay extends Command
{
    protected $signature = 'day:end';

    private GitCommand $git;
    private string $branch;
    private RunTests $tests;

    public function __construct(
        GitCommand $git,
        GetCurrentBranchName $branch,
        RunTests $tests
    ) {
        parent::__construct();
        $this->branch = $branch->execute();
        $this->tests = $tests;
        $this->git = $git;
    }

    public function handle()
    {
        if ($this->tests->execute() == 1) {
            if ($this->confirm('Your tests are not currently passing. Are you sure you want to quit?')) {
                return 1;
            }
        }

        $hours = $this->ask('How many hours of work did you do during this session?');

        $this->git->execute('git add .');
        $this->git->execute('git commit -m "End+of+Day+-+'.$hours.'+Hours" --allow-empty');
        $this->git->execute('git pull --rebase');
        $this->git->execute('git push');

        return 0;
    }
}
