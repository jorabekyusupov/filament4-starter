<?php

namespace Modules\ActivityLog\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class UserActivitiesPage extends \AlizHarb\ActivityLog\Pages\UserActivitiesPage
{

use HasPageShield;
    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('settings');
    }

}