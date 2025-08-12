<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelServiceProvider;
use Spatie\GoogleTagManager\GoogleTagManagerServiceProvider;
use App\Providers\CookiesServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelServiceProvider::class,
    GoogleTagManagerServiceProvider::class,
    CookiesServiceProvider::class,
];
