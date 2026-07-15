<?php

declare(strict_types=1);

namespace VendorName\Skeleton;

use Shopper\Core\Traits\HasRegisterConfigAndMigrationFiles;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VendorName\Skeleton\Console\InstallCommand;

final class SkeletonServiceProvider extends PackageServiceProvider
{
    use HasRegisterConfigAndMigrationFiles;

    protected string $root = __DIR__.'/..';

    /** @var list<string> */
    protected array $configFiles = [
        'skeleton_snake',
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('shopper-skeleton-slug')
            ->hasTranslations()
            ->hasViews()
            ->hasCommand(InstallCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->registerConfigFiles();
        $this->registerDatabase();
    }
}
