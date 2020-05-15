<?php


namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class SetRepositoryCommandTest extends TestCase
{
    private $composer;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('laravel-git-workflow.composer_json', __DIR__ . '/test_composer.json');
        Config::set('laravel-git-workflow.repositories', [
           'my-happy-package' => [
               'git' => 'https://github.com/edgrosvenor/my-happy-package',
               'path' => '../../packages/edgrosvenor/my-happy-package',
           ],
            'my-crazy-package' => [
                'git' => 'https://github.com/edgrosvenor/my-crazy-package',
                'path' => '../../packages/edgrosvenor/my-crazy-package',
            ],
            'my-sad-package' => [
                'path' => '../../packages/edgrosvenor/my-sad-package',
            ],
        ]);

        $this->composer = File::get(config('laravel-git-workflow.composer_json'));
    }

    public function tearDown(): void
    {
        File::put(config('laravel-git-workflow.composer_json'), $this->composer);
        File::put(config('laravel-git-workflow.composer_json'), $this->composer);
    }

    /** @test */
    public function it_happily_switches_between_two_repos()
    {
        $composer = json_decode($this->composer, true);

        $this->assertEquals(['type' => 'git', 'url' => 'https://github.com/edgrosvenor/my-crazy-package'], $composer['repositories'][2]);

        $this->artisan('repo my-crazy-package git');

        $updated = json_decode(File::get(config('laravel-git-workflow.composer_json')), true);

        $this->assertEquals(['type' => 'git', 'url' => 'https://github.com/edgrosvenor/my-crazy-package'], $updated['repositories'][2]);

        $this->artisan('repo my-crazy-package path');

        $updated = json_decode(File::get(config('laravel-git-workflow.composer_json')), true);

        $this->assertEquals(['type' => 'path', 'url' => '../../packages/edgrosvenor/my-crazy-package'], $updated['repositories'][2]);

        $this->artisan('repo my-crazy-package packagist');

        $updated = File::get(config('laravel-git-workflow.composer_json'));

        $this->assertStringNotContainsString('my-crazy-package', $updated);

        $this->artisan('repo my-crazy-package path');

        $updated = json_decode(File::get(config('laravel-git-workflow.composer_json')), true);

        $this->assertEquals(['type' => 'path', 'url' => '../../packages/edgrosvenor/my-crazy-package'], $updated['repositories'][2]);

        $this->artisan('repo my-crazy-package git');

        $updated = json_decode(File::get(config('laravel-git-workflow.composer_json')), true);

        $this->assertEquals(['type' => 'git', 'url' => 'https://github.com/edgrosvenor/my-crazy-package'], $updated['repositories'][2]);
    }

    /** @test */
    public function it_removes_the_repo_if_not_defined_for_a_package()
    {
        $this->assertStringContainsString('../../packages/edgrosvenor/my-sad-package', $this->composer);

        $this->artisan('repo my-sad-package git');

        $updated = File::get(config('laravel-git-workflow.composer_json'));

        $this->assertStringNotContainsString('my-sad-package', $updated);

        $this->assertIsArray(json_decode($updated, true));
    }

    /** @test */
    public function it_sets_a_repo_to_the_local_path()
    {
        $this->assertStringNotContainsString('../../packages/edgrosvenor/my-happy-package', $this->composer);

        $this->artisan('repo my-happy-package path');

        $updated = File::get(config('laravel-git-workflow.composer_json'));

        $this->assertStringContainsString('../../packages/edgrosvenor/my-happy-package', $updated);

        $this->assertIsArray(json_decode($updated, true));
    }
}
