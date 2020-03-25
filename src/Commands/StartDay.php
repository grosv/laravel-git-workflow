<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\Actions\ParseGitBranches;
use Grosv\LaravelGitWorkflow\Actions\ParseGitHubIssues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class StartDay extends Command
{

    protected $signature = 'day:start';
    protected $description = '';
    protected $help = '';

    /** @var ParseGitHubIssues  */
    private $issues;

    /** @var ParseGitBranches  */
    private $branches;

    /** @var GitCommand  */
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

        $process = new Process(['gh', '--help']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('✖️ You must install the GitHub CLI. See https://cli.github.com');
            exit(1);
        }
        $this->info('✔️ The Symfony process runner seems to be working');

        $this->info('✔️ GitHub CLI appears to be installed');

        $this->git->execute('git checkout master');

        $this->info('✔️ Checked out master');
        $this->git->execute('git pull --rebase');

        $this->info('✔️ Master is up to date');

        $open = $this->issues->execute($this->git->execute('gh issue list')->getOutput());

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