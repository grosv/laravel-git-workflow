<?php

namespace Tests;

use Grosv\LaravelGitWorkflow\Actions\GitCommand;
use Grosv\LaravelGitWorkflow\LaravelGitWorkflowProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->mock(GitCommand::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->andReturn(new GitCommand(''));
        });

    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [LaravelGitWorkflowProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.key', 'base64:r0w0xC+mYYqjbZhHZ3uk1oH63VadA3RKrMW52OlIDzI=');
    }
}