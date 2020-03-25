<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SetBranchForIssue
{

    private $branches;
    private $git;
    private ?string $branch;

    public function __construct(GitCommand $git, ParseGitBranches $branches)
    {
        $this->branches = $branches;
        $this->git = $git;
        $this->branch = null;
    }

    public function execute(string $issue): void
    {

        $this->branch = config('laravel-git-workflow.branch_prefix') . $issue;

        $branches = $this->branches->execute($this->git->execute('git branch -a')->getOutput());

        if (in_array($issue, $branches)) {
            $this->git->execute('git checkout ' . $this->branch);
            $this->git->execute('git fetch');
            $this->git->execute('git rebase origin/master');

            return;
        }

        $this->git->execute('git checkout -b ' . $this->branch);
        $this->git->execute('git commit --allow-empty -m "WIP"');
        $this->git->execute('git push -u origin ' . $this->branch);
        $this->git->execute('gh pr create -t ' . Str::title(str_replace('_', ' ', $issue)) . ' -b "WIP" -d');
    }
}