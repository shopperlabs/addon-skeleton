<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Shopper\Models\Permission;
use Shopper\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

#[AsCommand(name: 'shopper:skeleton-slug:install')]
final class InstallCommand extends Command
{
    protected $signature = 'shopper:skeleton-slug:install';

    protected $description = 'Install the addon: run migrations, create permissions and register the addon';

    public function handle(): int
    {
        intro('Installing Shopper Skeleton');

        $this->task('Running database migrations', function (): void {
            $this->callSilently('migrate', ['--force' => true]);
        });

        $this->registerPermissions();
        $this->registerAddon();

        outro('Shopper Skeleton is installed.');

        return self::SUCCESS;
    }

    private function task(string $title, Closure $callback): void
    {
        spin(callback: $callback, message: $title);

        $width = 52;
        $dots = str_repeat('.', max(1, $width - mb_strlen($title)));

        $this->output->writeln("  <fg=#94A3B8>{$title}</> <fg=#334155>{$dots}</> <fg=#22C55E>✓</>");
    }

    private function registerPermissions(): void
    {
        $this->task('Creating skeleton_snake.* permissions', function (): void {
            Permission::generate('skeleton_snake');

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $administrator = Role::query()
                ->where('name', config('shopper.admin.roles.admin', 'administrator'))
                ->first();

            $administrator?->permissions()->syncWithoutDetaching(
                Permission::query()
                    ->where('name', 'like', 'skeleton_snake.%')
                    ->pluck('id')
                    ->all(),
            );
        });
    }

    private function registerAddon(): void
    {
        $path = app_path('Providers/AppServiceProvider.php');

        if (! File::exists($path)) {
            $this->manualRegistrationNotice();

            return;
        }

        $contents = File::get($path);

        if (str_contains($contents, 'SkeletonAddon')) {
            note('SkeletonAddon is already registered in AppServiceProvider.');

            return;
        }

        $updated = $this->addImports($contents);
        $updated = $this->addAddonRegistration($updated);

        if ($updated === null) {
            $this->manualRegistrationNotice();

            return;
        }

        $this->task('Registering SkeletonAddon in AppServiceProvider', function () use ($path, $updated): void {
            File::put($path, $updated);
        });
    }

    private function addImports(string $contents): string
    {
        $imports = '';

        if (! str_contains($contents, 'use Shopper\Facades\Shopper;')) {
            $imports .= "use Shopper\\Facades\\Shopper;\n";
        }

        if (! str_contains($contents, 'use VendorName\Skeleton\SkeletonAddon;')) {
            $imports .= "use VendorName\\Skeleton\\SkeletonAddon;\n";
        }

        if ($imports === '') {
            return $contents;
        }

        return (string) preg_replace('/^(namespace [^;]+;\n\n?)/m', '$1'.$imports, $contents, 1);
    }

    private function addAddonRegistration(string $contents): ?string
    {
        if (str_contains($contents, 'Shopper::addons([')) {
            return str_replace(
                'Shopper::addons([',
                "Shopper::addons([\n            new SkeletonAddon,",
                $contents,
            );
        }

        $patched = preg_replace(
            '/(public function register\(\): void\s*\{)/',
            "$1\n        Shopper::addons([\n            new SkeletonAddon,\n        ]);\n",
            $contents,
            1,
            $count,
        );

        return $count === 1 ? $patched : null;
    }

    private function manualRegistrationNotice(): void
    {
        warning('Could not update AppServiceProvider automatically. Register the addon manually:');
        note('Shopper::addons([new \VendorName\Skeleton\SkeletonAddon]);');
    }
}
