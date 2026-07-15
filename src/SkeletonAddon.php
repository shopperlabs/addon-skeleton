<?php

declare(strict_types=1);

namespace VendorName\Skeleton;

use Illuminate\Support\Facades\Route;
use Shopper\Addon\BaseAddon;
use Shopper\ShopperPanel;
use VendorName\Skeleton\Livewire\SkeletonIndex;
use VendorName\Skeleton\Sidebar\SkeletonSidebar;

final class SkeletonAddon extends BaseAddon
{
    /** @var array<string, class-string> */
    private array $componentOverrides = [];

    public function getId(): string
    {
        return 'skeleton-slug';
    }

    /**
     * @param  array<string, class-string>  $components
     */
    public function usingLivewireComponents(array $components): static
    {
        $this->componentOverrides = array_merge($this->componentOverrides, $components);

        return $this;
    }

    public function register(ShopperPanel $panel): void
    {
        $components = $this->livewireComponents();

        $panel
            ->addonLivewireComponents($components)
            ->addonSidebar(SkeletonSidebar::class)
            ->addonRoutes(function () use ($components): void {
                Route::prefix('skeleton-slug')
                    ->as('skeleton-slug.')
                    ->group(function () use ($components): void {
                        Route::get('/', $components['skeleton-slug.index'])->name('index');
                    });
            });
    }

    /**
     * @return array<string, class-string>
     */
    private function livewireComponents(): array
    {
        $components = [
            'skeleton-slug.index' => SkeletonIndex::class,
        ];

        return array_merge($components, array_intersect_key($this->componentOverrides, $components));
    }
}
