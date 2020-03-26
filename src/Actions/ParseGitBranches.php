<?php

namespace Grosv\LaravelGitWorkflow\Actions;

class ParseGitBranches
{
    public array $branches;

    public function __construct()
    {
        $this->branches = [];
    }

    public function execute(string $string)
    {
        foreach (explode("\n", $string) as $branch) {
            array_push($this->branches, trim(str_replace('*', '', $branch)));
        }

        return $this->branches;
    }
}
