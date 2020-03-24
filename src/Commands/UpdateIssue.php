<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class UpdateIssue extends Command
{
    protected $signature = 'commit {message?}';
    public ?string $branch;
    public ?string $message;

    public function __construct()
    {
        parent::__construct();
        $this->branch = null;
    }

    public function handle()
    {
        $this->message = $this->argument('message');
        $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD']);
        $process->run();
        $this->branch = trim($process->getOutput());

        if ($this->branch === 'master')
        {
            $this->error('You cannot commit directly to the master branch. Run `php artisan issue:start` to get to your feature branch.');
            return;
        }

        if ($this->message) {
            $this->message = $this->ask('In one sentence, what changes have you made since your last commit?');
        }

        $process = new Process(['git', 'add', '.']);
        $process->run();

        $process = new Process(['git', 'commit', $this->message]);
        $process->run();

        $process = new Process(['git', 'push']);
        $process->run();

        if ($this->confirm('Are you ready to close this issue and request a review of the pull request?')) {
            Artisan::call('issue:close ' . $this->branch);
        }

        $this->info('Your commit has been added to the pull request.');

    }
}