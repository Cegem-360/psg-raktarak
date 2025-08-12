<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use GoogleTagManager;
use Illuminate\Support\ServiceProvider;
use Whitecube\LaravelCookieConsent\Facades\Cookies;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers

        // Set locale from session
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        GoogleTagManager::set('config', env('GOOGLE_TAG_ID', ''));
        if (Cookies::hasConsentFor('ad_storage')) {
            GoogleTagManager::set('ad_storage', 'granted');
        }

        if (Cookies::hasConsentFor('ad_user_data')) {
            GoogleTagManager::set('ad_user_data', 'granted');
        }

        if (Cookies::hasConsentFor('ad_personalization')) {
            GoogleTagManager::set('ad_personalization', 'granted');
        }

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->translateLabel();
        });
        RichEditor::configureUsing(function (RichEditor $editor): void {
            $editor->translateLabel();
        });
        FileUpload::configureUsing(function (FileUpload $upload): void {
            $upload->translateLabel();
        });

        TextInput::configureUsing(function (TextInput $input): void {
            $input->translateLabel();
        });
        Toggle::configureUsing(function (Toggle $toggle): void {
            $toggle->translateLabel();
        });
        ImageColumn::configureUsing(function (ImageColumn $column): void {
            $column->translateLabel();
        });
        IconColumn::configureUsing(function (IconColumn $column): void {
            $column->translateLabel();
        });

    }
}
