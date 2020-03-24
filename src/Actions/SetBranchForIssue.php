<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SetBranchForIssue
{

    public function execute(string $issue): void
    {
        $process = new Process(['git', 'branch', '-a']);
        $process->run();
        $branches = (new ParseGitBranches())->execute($process->getOutput());

        if (!in_array($issue, $branches)) {
            $process = new Process(['git', 'checkout', '-b', $issue]);
            $process->run();
        }
        else {
            $process = new Process(['git', 'checkout', $issue]);
            $process->run();

            $process = new Process(['git', 'add', '.']);
            $process->run();

            $process = new Process(['git', 'commit', '-m', 'WIP']);
            $process->run();
        }

        if (!in_array('remotes/origin/' . $issue, $branches)) {
            touch(base_path($issue));
            File::append(base_path($issue), date('r') . ' Started Pull Request For ' . $issue);
            $process = new Process(['git', 'add', '.']);
            $process->run();

            $process = new Process(['git', 'commit', '-m', 'WIP']);
            $process->run();

            $process = new Process(['git', 'push', '-u', 'origin', $issue]);
            $process->run();

            $process = new Process(['gh', 'pr', 'create', '-t', Str::title(str_replace('_', ' ', $issue)), '-b', 'WIP', '-d']);
            $process->run();
        }

        $process = new Process(['git', 'fetch']);
        $process->run();

        $process = new Process(['git', 'rebase', 'origin/master']);
        $process->run();

    }
}