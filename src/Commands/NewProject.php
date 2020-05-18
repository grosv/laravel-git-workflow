<?php


namespace Grosv\LaravelGitWorkflow\Commands;


use Faker\Generator;
use Grosv\LaravelGitWorkflow\Actions\CreateLocalDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TitasGailius\Terminal\Terminal;

class NewProject extends Command
{
    protected $signature = 'project:start';

    private bool $launchChecklist = false;
    private array $colors;
    private array $footers;
    private array $layouts;
    private array $theme;

    public function __construct(Generator $faker)
    {
        parent::__construct();
        $this->faker = $faker;
        $this->colors = ['gray', 'red', 'orange', 'yellow', 'green', 'teal', 'blue', 'indigo', 'purple', 'pink'];
        $this->footers = ['simple_social'];
        $this->layouts = ['droopy'];
    }

    public function handle()
    {
        $db = $this->choice('What database do you want to use for your local development?', ['MySQL', 'sqlite', 'None']);

        if ($db == 'sqlite') {
            Terminal::run('touch ' . database_path('database.sqlite'));
            File::append(base_path('.env'), File::get(__DIR__.'/stubs/env_sqlite.stub'));
        }

        if ($db == 'MySQL') {
            $db_username = $this->ask('What is your database username?', 'root');
            $db_password = $this->ask('What is the password for ' . $db_username .'?', '');
            $db_database = $this->ask('What do you want to name your database?', Str::snake($this->faker->words(2, true)));

            $env = str_replace(['{DB_USERNAME}', '{DB_PASSWORD}', '{DB_DATABASE}'],
                [$db_username, $db_password, $db_database],
                File::get(__DIR__.'/stubs/env_mysql.stub')
            );

            File::append(base_path('.env'), $env);

            Config::set('database.db_username', $db_username);
            Config::set('database.db_password', $db_password);

            if (!(new CreateLocalDatabase($db_database))->execute()) {
                $this->error('There was a problem creating the database.');
            }
        }

        $issues = Terminal::run('gh issue list');

        // Create Launch Checklist Issue if Not Exists
        foreach ($issues->lines() as $line) {
            if (Str::contains((string)$line, 'Launch Checklist')) {
                $this->launchChecklist = true;
            }
        }

        if (!$this->launchChecklist) {
            $response = Terminal::run('gh issue create --title="Launch Checklist" --body="'. File::get(base_path('LAUNCH.md')) .'"');
            if (!$response->ok()) {
                $this->error('Failed to create launch checklist issue!');
                return 1;
            }
            $this->info('Launch checklist issue created.');
        }

        $this->info('The next few questions will create a new config/theme.php file where you can edit the values if you change your mind.');

        $this->theme['color'] = $this->choice('What theme color would you like to use?', ['gray', 'red', 'orange', 'yellow', 'green', 'teal', 'blue', 'indigo', 'purple', 'pink']);

        $this->theme['facebook'] = $this->ask('Facebook Username (optional)');
        $this->theme['instagram'] = $this->ask('Instagram Username (optional)');
        $this->theme['twitter'] = $this->ask('Twitter Username (optional)');
        $this->theme['github'] = $this->ask('GitHub Username (optional)');

        $this->theme['footer'] = $this->choice('Which footer template do you want to use?', $this->footers);

        $this->theme['layout'] = $this->choice('Which theme layout do you want to user?', $this->layouts);

        $this->theme['copyright'] = $this->ask('Who is the copyright holder for this site (optional)?');

        $before = $after = [];
        foreach ($this->theme as $k => $v) {
            $before[] = '{'.$k.'}';
            $after[] = $v;
        }

        $theme = str_replace($before, $after, File::get(__DIR__ . '/stubs/theme.stub'));

        File::put(base_path('config/theme.php'), $theme);

        return 0;
    }
}