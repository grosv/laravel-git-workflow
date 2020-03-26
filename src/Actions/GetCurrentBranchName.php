<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Symfony\Component\Process\Process;

class GetCurrentBranchName
{
    public function execute(): string
    {
        $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD']);
        $process->run();
        return trim($process->getOutput());
    }
}