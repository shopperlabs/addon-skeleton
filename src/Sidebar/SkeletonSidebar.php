<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Sidebar;

use Shopper\Sidebar\AbstractAdminSidebar;
use Shopper\Sidebar\Contracts\Builder\Group;
use Shopper\Sidebar\Contracts\Builder\Item;
use Shopper\Sidebar\Contracts\Builder\Menu;

final class SkeletonSidebar extends AbstractAdminSidebar
{
    public function extendWith(Menu $menu): Menu
    {
        $menu->group(__('shopper-skeleton-slug::messages.menu'), function (Group $group): void {
            $group->weight(6);
            $group->setAuthorized($this->user->hasPermissionTo('skeleton_snake.browse'));

            $group->item(__('shopper-skeleton-slug::messages.menu'), function (Item $item): void {
                $item->weight(1);
                $item->setAuthorized($this->user->hasPermissionTo('skeleton_snake.browse'));
                $item->route('shopper.skeleton-slug.index');
                $item->setIcon('untitledui-grid');
                $item->useSpa();
            });
        });

        return $menu;
    }
}
