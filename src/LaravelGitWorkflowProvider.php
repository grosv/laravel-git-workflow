<?php


namespace Grosv\LaravelGitWorkflow;


use Grosv\LaravelGitWorkflow\Commands\CloseIssue;
use Grosv\LaravelGitWorkflow\Commands\StartIssue;
use Grosv\LaravelGitWorkflow\Commands\StartDay;
use Grosv\LaravelGitWorkflow\Commands\UpdateIssue;
use Illuminate\Support\ServiceProvider;

class LaravelGitWorkflowProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CloseIssue::class,
                StartDay::class,
                StartIssue::class,
                UpdateIssue::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-package-template');
    }
}