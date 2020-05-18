<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Terminal;

class VaporDeploy extends Command
{
    protected $signature = 'vapor {environment}';

    public function handle()
    {
        foreach ($this->repos as $k => $v) {
            $this->call('repo', ['package' => $k, 'repo' => 'git']);
        }

        Terminal::output($this)->run('vapor deploy ' . $this->argument('environment'));

        foreach ($this->repos as $k => $v) {
            $this->call('repo', ['package' => $k, 'repo' => 'path']);
        }
    }
}