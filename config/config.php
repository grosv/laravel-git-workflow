<?php

return [
    'branch_prefix' => env('LGW_BRANCH_PREFIX', ''),
    'project_owner' => env('LGW_PROJECT_OWNER', ''),
    'github_user'   => env('LGW_GITHUB_USER', ''),
    'wip'           => env('LGW_WIP', 'WIP'),
    'env'           => base_path('.env'),
    'composer_json' => base_path('/composer.json'),
    'repositories'  => [],
];
