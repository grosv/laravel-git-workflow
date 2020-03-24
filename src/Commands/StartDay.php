<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Grosv\LaravelGitWorkflow\Actions\ParseGitBranches;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StartDay extends Command
{

    protected $signature = 'day:start';
    protected $description = '';
    protected $help = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(ParseGitHubIssues $issues, ParseGitBranches $branches)
    {
        $process = new Process(['nosuchcommandexistsiamsure']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->info('✔️ The Symfony process runner seems to be working');
        }
        $process = new Process(['gh', '--help']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('✖️ You must install the GitHub CLI. See https://cli.github.com');
            exit(1);
        }

        $this->info('✔️ GitHub CLI appears to be installed');

        $process = new Process(['git', 'checkout', 'master']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('✖️ Failed to check out the master branch.');
        }

        $this->info('✔️ Checked out master');

        $process = new Process(['git', 'pull', '--rebase']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('✖️ Failed to check out the master branch.');
        }

        $this->info('✔️ Master is up to date');

        $process = new Process(['gh', 'issue', 'list']);
        $process->run();

        $open = $issues->execute($process->getOutput());

        if (empty($open)) {
            $this->info('✔️ There are no open issues at this time');
            return;
        }

        $open[0] = 'None Right Now';

        $issue = $this->choice('Which issue would you like to work on?', $open, 0);

        if ($issue !== 0 && $issue !== 'None Right Now') {
            Artisan::call('issue:start ' . $issue);
        }






    }
}