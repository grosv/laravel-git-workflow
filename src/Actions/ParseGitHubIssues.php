<?php

namespace Grosv\LaravelGitWorkflow\Actions;

use Illuminate\Support\Str;

class ParseGitHubIssues
{
    public array $issues;

    public function __construct()
    {
        $this->issues = [];
    }

    public function execute(string $string)
    {
        foreach (explode("\n", $string) as $issue) {
            if (count(explode("\t", $issue)) > 1) {
                $this->issues[explode("\t", $issue)[0]] = Str::snake(
                    preg_replace('/[^a-z0-9 ]/i', '', explode("\t", $issue)[0].' '.explode("\t", $issue)[1])
                );
            }
        }

        return $this->issues;
    }
}
