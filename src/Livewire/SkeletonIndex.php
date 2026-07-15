<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Livewire;

use Illuminate\Contracts\View\View;
use Shopper\Livewire\Pages\AbstractPageComponent;
use Shopper\Traits\HandlesAuthorizationExceptions;

class SkeletonIndex extends AbstractPageComponent
{
    use HandlesAuthorizationExceptions;

    public function mount(): void
    {
        $this->authorize('skeleton_snake.browse');
    }

    public function render(): View
    {
        return view('shopper-skeleton-slug::livewire.pages.skeleton-slug-index')
            ->title(__('shopper-skeleton-slug::messages.title'));
    }
}
