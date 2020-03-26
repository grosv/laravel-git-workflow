<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Symfony\Component\Process\Process;

class RunTests
{
    public function execute()
    {
        $process = new Process(['phpunit']);
        $process->run();
        return $process->getExitCode();
    }

}