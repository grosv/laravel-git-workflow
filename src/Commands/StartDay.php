<?php

namespace Grosv\LaravelGitWorkflow\Commands;

use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\ParseGitBranches;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class StartDay extends Command
{
    protected $signature = 'day:start';
    protected $description = '';
    protected $help = '';

    /** @var ParseGitHubIssues */
    private $issues;

    /** @var ParseGitBranches */
    private $branches;

    /** @var GitCommand */
    private $git;

    public function __construct(ParseGitHubIssues $issues, ParseGitBranches $branches, GitCommand $git)
    {
        parent::__construct();
        $this->issues = $issues;
        $this->branches = $branches;
        $this->git = $git;
    }

    public function handle()
    {
        if (config('laravel-git-workflow.github_user') === '') {
            $gh = $this->ask('What is your GitHub username? (https://github.com/your_username)');
            File::append(config('laravel-git-workflow.env'), "\nLGW_GITHUB_USER=".$gh);
            Config::set('laravel-git-workflow.github_user', $gh);
        }

        $process = new Process(['gh', '--help']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('✖️ You must install the GitHub CLI. See https://cli.github.com');
            exit(1);
        }

        $this->info('✔️ GitHub CLI appears to be installed');

        $this->git->execute('git checkout '.config('laravel-git-workflow.trunk'));

        $this->info('✔️ Checked out '.config('laravel-git-workflow.trunk'));
        $this->git->execute('git pull --rebase');

        $this->info('✔️ '.Str::title(config('laravel-git-workflow.trunk')).' is up to date');

        $issues = $this->git->execute('gh issue list')->getOutput();
        $open = $this->issues->execute($issues);

        if (empty($open)) {
            $this->info('✔️ There are no open issues at this time');

            return;
        }

        $open[0] = 'None Right Now';

        $issue = $this->choice('Which issue would you like to work on?', $open, 0);

        if ($issue != 0 && $issue !== 'None Right Now') {
            $this->call('issue:start', ['issue' => $issue]);
        }
    }
}
