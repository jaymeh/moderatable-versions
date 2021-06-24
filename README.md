## Package Details

This package is an extension on top of https://github.com/mpociot/versionable and aims to provide some simple moderation on top of versionable models. This is achieved by adding an `approved_at` timestamp field and an optional `approved_by` field onto the existing version table.

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

## Restrictions

There are a few restrictions in place due to the implementation of this package. One of which is because versions have to be associated to a model, upon creating a new model it is always set to be approved.

You can work around this by combining some other package like: https://github.com/pawelmysior/laravel-publishable to add publishable status' to your models or alternatively roll your own implementation.

## Roadmap
There are a number of features which would be nice to have but haven't made it into this version just yet. These are:

    * Toggling approval on or off - At times you may want to disable approval under certain circumstances. It would therefore be useful to allow you to toggle this on or off via a method on the trait.