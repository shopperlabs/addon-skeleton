# :package_description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/:vendor_slug/:package_slug/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/:vendor_slug/:package_slug/actions/workflows/tests.yml)
[![GitHub Code Quality Action Status](https://img.shields.io/github/actions/workflow/status/:vendor_slug/:package_slug/quality.yml?branch=main&label=code%20quality&style=flat-square)](https://github.com/:vendor_slug/:package_slug/actions/workflows/quality.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)

<!--delete-->
---
This repo can be used to scaffold a [Shopper](https://github.com/shopperlabs/shopper) addon. Follow these steps to get started:

1. Press the "Use this template" button at the top of this repo to create a new repo with the contents of this skeleton.
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files.
3. Make something great!
---
<!--/delete-->

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require :vendor_slug/:package_slug
```

Then run the installer:

```bash
php artisan shopper:skeleton-slug:install
```

It runs the migrations, creates the `skeleton_snake.*` permissions (granted to
the administrator role), and registers `SkeletonAddon` in your
`AppServiceProvider`. Re-running it is safe.

## Usage

The addon ships its own admin page, sidebar entry and Livewire components.
Every component can be overridden from your application:

```php
Shopper::addons([
    (new SkeletonAddon)->usingLivewireComponents([
        'skeleton-slug.index' => \App\Livewire\CustomSkeletonIndex::class,
    ]),
]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
