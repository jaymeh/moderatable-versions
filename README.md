## Package Details

This package is an extension on top of https://github.com/mpociot/versionable and aims to provide some simple moderation on top of versionable models. This is achieved by adding an `approved_at` date field onto the existing version table.

The goal is to keep the functionality simple, minimalistic and flexible.

## Installation

You can install via composer:
`composer require jaymeh/moderatable-versions`

Run the migrations:
`php artisan migrate --path=vendor/jaymeh/moderatable-versions/src/migrations`

Alternatively, publish the migrations:
`php artisan vendor:publish --provider="Jaymeh\ModeratableVersions\Providers\ServiceProvider" --tag="migrations"`

Then customize and run them.
`php artisan migration`




