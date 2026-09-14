<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;

/**
 * Every option of the `parkolas` select in PropertyResource must have an English
 * translation in lang/en.json, otherwise the raw Hungarian value leaks onto the
 * English property page.
 */
it('translates every parking option to english', function (string $hungarian, string $english): void {
    App::setLocale('en');

    expect(__($hungarian))->toBe($english);
})->with([
    ['felszíni parkoló', 'surface parking'],
    ['teremgarázs', 'underground garage'],
    ['felszíni parkoló és teremgarázs', 'surface & underground parking'],
]);
