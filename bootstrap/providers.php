<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\CookiesServiceProvider;
use App\Providers\Filament\AdminPanelServiceProvider;
use Spatie\GoogleTagManager\GoogleTagManagerServiceProvider;

return [
    AppServiceProvider::class,
    CookiesServiceProvider::class,
    AdminPanelServiceProvider::class,
    GoogleTagManagerServiceProvider::class,
];
