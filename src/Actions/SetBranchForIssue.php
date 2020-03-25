<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SetBranchForIssue
{

    public array $branches;

    public function __construct()
    {
        $this->branches = [];
    }

    public function execute(string $issue, GitCommand $git, ParseGitBranches $branches): void
    {

        $this->branches = $branches->execute($git->execute('git branch -a')->getOutput());

        if (in_array($issue, $this->branches)) {
            $git->execute('git checkout ' . $issue);
            $git->execute('git fetch');
            $git->execute('git rebase origin/master');

            return;
        }

        $git->execute('git checkout -b ' . $issue);
        $git->execute('git commit --allow-empty -m "WIP"');
        $git->execute('git push -u origin ' . $issue);
        $git->execute('gh pr create -t ' . Str::title(str_replace('_', ' ', $issue)) . ' -b "WIP" -d');
    }
}