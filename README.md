# Laravel Git Workflow

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grosv/laravel-git-workflow.svg?style=flat-square)](https://packagist.org/packages/grosv/laravel-git-workflow)
[![StyleCI](https://github.styleci.io/repos/248610774/shield?branch=master)](https://github.styleci.io/repos/248610774)
![Build Status](https://app.chipperci.com/projects/991579ec-e338-486f-8e93-9025167700ad/status/master)

An opinionated GitHub workflow I use to manage my team and any freelancers I work with. All our projects use short lived feature branches off of master. We use draft pull requests and at least daily pushes so that I can track progress and identify "stuck" developers quickly.

### Installation
```shell script
composer require grosv/laravel-git-workflow
```

This adds a handful of commands to your Laravel app.

### Commands

`php artisan day:start` Verifies that we have the developer's GitHub username, checks out master and ensures it's up to date. Prompts developer to choose which issue to work on.

`php artisan issue:start {issue}` Checks out the branch associated with the issue. Creates a remote branch and draft pull request if they don't exist.

`php artisan commit` Ensures the developer is on a feature branch and then commits / pushes to it.

`php artisan issue:close {issue}` (Not Ideal) Puts up an empty commit with the project owner tagged in the message requesting a review and prompts the developer to go to github.com to mark the PR ready for review. 

`php artisan day:end` Asks the developer how many hours they put in during the day and commits whatever has not yet been committed to the PR.
