<?php

namespace Grosv\LaravelGitWorkflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo {package} {repo}';

    public string $package;
    public string $repo;

    public function handle(): int
    {
        $this->package = $this->argument('package');
        $this->repo = $this->argument('repo');

        $packages = config('laravel-git-workflow.repositories') ?? [];

        if (!isset($packages[$this->package])) {
            $this->error('You have not configured a package called '.$this->package);
        }

        $new = $packages[$this->package];

        $composer = json_decode(File::get(config('laravel-git-workflow.composer_json')), true);

        $found = false;

        foreach ($composer['repositories'] as $k => $v) {
            if (Str::contains($v['url'], $this->package)) {
                if (!isset($new[$this->repo])) {
                    unset($composer['repositories'][$k]);
                } else {
                    $composer['repositories'][$k]['url'] = $new[$this->repo];
                    $composer['repositories'][$k]['type'] = $this->repo;
                }
                $found = true;
            }
        }

        if (!$found) {
            $composer['repositories'][] = ['url' => $new['path'], 'type' => $this->repo];
        }

        File::put(config('laravel-git-workflow.composer_json'), json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        unset($composer);

        return 0;
    }
}
