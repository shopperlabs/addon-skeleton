<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Shopper\Core\CoreServiceProvider;
use Shopper\Core\Database\Seeders\CurrenciesTableSeeder;
use VendorName\Skeleton\SkeletonServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = CurrenciesTableSeeder::class;

    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            CoreServiceProvider::class,
            SkeletonServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');

        $app->afterResolving('migrator', function ($migrator): void {
            $migrator->path(\Orchestra\Testbench\default_migration_path());
        });
    }
}
