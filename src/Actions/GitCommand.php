<?php

namespace Grosv\LaravelGitWorkflow\Actions;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCommand
{
    /** @var Process */
    public $process;
    public string $output;
    public array $command;

    public function __construct(string $output = '')
    {
        $this->output = $output;
        $this->command = [];
    }

    public function execute(string $command): self
    {
        $this->command = [];
        $commandArray = explode(' ', $command);
        foreach ($commandArray as $item) {
            $this->command[] = urldecode($item);
        }
        $this->process = new Process($this->command);
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
