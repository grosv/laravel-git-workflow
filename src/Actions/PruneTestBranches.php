<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Str;

class PruneTestBranches
{
    public function execute() :void
    {
        $branches = explode("\n", (new GitCommand())->execute('git branch -a')->getOutput());

        foreach ($branches as $branch) {
            if (Str::startsWith(trim($branch), 'lgw_')) {
                (new GitCommand())->execute('git branch -D ' .$branch);
                (new GitCommand())->execute('git push origin --delete remotes/origin/' . $branch);
            }
        }
    }
}