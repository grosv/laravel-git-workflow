<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCommand
{
    /** @var Process */
    public $process;
    public string $output;

    public function __construct()
    {
        $this->output = '';
    }

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
        return is_object($this->process) ? $this->process->getOutput() : $this->output;
    }
}