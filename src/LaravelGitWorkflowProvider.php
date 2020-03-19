<?php


namespace Grosv\LaravelGitWorkflow;


use Illuminate\Support\ServiceProvider;

class LaravelGitWorkflowProvider extends ServiceProvider
{
    public function boot(): void
    {

    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-package-template');
    }
}