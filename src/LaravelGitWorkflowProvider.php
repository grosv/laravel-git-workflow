<?php

namespace Grosv\LaravelGitWorkflow;

use Grosv\LaravelGitWorkflow\Commands\CloseIssue;
use Grosv\LaravelGitWorkflow\Commands\EndDay;
use Grosv\LaravelGitWorkflow\Commands\NewProject;
use Grosv\LaravelGitWorkflow\Commands\SetRepository;
use Grosv\LaravelGitWorkflow\Commands\StartDay;
use Grosv\LaravelGitWorkflow\Commands\StartIssue;
use Grosv\LaravelGitWorkflow\Commands\UpdateIssue;
use Illuminate\Support\ServiceProvider;

class LaravelGitWorkflowProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CloseIssue::class,
                EndDay::class,
                StartDay::class,
                StartIssue::class,
                UpdateIssue::class,
                SetRepository::class,
                NewProject::class,
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-git-workflow.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-git-workflow');
    }
}
