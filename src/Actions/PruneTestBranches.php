<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Str;

class PruneTestBranches
{
    /** @var GitCommand  */
    private $git;

    public function __construct(GitCommand $git)
    {
        $this->git = $git;
    }
    public function execute() :void
    {
        $branches = explode("\n", $this->git->execute('git branch -a')->getOutput());

        foreach ($branches as $branch) {
            if (Str::startsWith(trim($branch), 'lgw_')) {
                $this->git->execute('git branch -D ' .$branch);
                $this->git->execute('git push origin --delete remotes/origin/' . $branch);
            }
        }
    }
}