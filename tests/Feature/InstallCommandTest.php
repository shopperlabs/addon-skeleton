<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

const PROVIDER_STUB = <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}
PHP;

function providerPath(): string
{
    return app_path('Providers/AppServiceProvider.php');
}

beforeEach(function (): void {
    $this->originalProvider = File::exists(providerPath()) ? File::get(providerPath()) : null;
    File::ensureDirectoryExists(dirname(providerPath()));
});

afterEach(function (): void {
    if ($this->originalProvider !== null) {
        File::put(providerPath(), $this->originalProvider);
    }
});

it('registers the addon in the application service provider', function (): void {
    File::put(providerPath(), PROVIDER_STUB);

    $this->artisan('shopper:skeleton-slug:install')->assertSuccessful();

    $contents = File::get(providerPath());

    expect($contents)->toContain('use VendorName\Skeleton\SkeletonAddon;')
        ->and($contents)->toContain('new SkeletonAddon,');
});

it('is idempotent', function (): void {
    File::put(providerPath(), PROVIDER_STUB);

    $this->artisan('shopper:skeleton-slug:install')->assertSuccessful();
    $after = File::get(providerPath());

    $this->artisan('shopper:skeleton-slug:install')->assertSuccessful();

    expect(File::get(providerPath()))->toBe($after);
});
