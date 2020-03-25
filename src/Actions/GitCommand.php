<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCommand
{
    /** @var Process */
    public $process;

    public function execute(string $command): self
    {
        $this->process = new Process(explode(' ', $command));
        $this->process->run();

        if (!$this->process->isSuccessful()) {
            throw new ProcessFailedException($this->process);
        }
        return $this;
    }

    public function getOutput()
    {
        return $this->process->getOutput();
    }
}