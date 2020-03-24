<?php


namespace Grosv\LaravelGitWorkflow\Actions;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParseGitHubIssues
{
    public array $issues;

    public function __construct()
    {
        $this->issues = [];
    }

    public function execute(String $string)
    {
        foreach (explode("\n", $string) as $issue) {
            if (sizeof(explode("\t", $issue)) > 1) {
                $this->issues[explode("\t", $issue)[0]] = Str::snake(explode("\t", $issue)[0] . ' ' . explode("\t", $issue)[1]);
            }
        }
        return $this->issues;
    }
}