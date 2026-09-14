<?php

declare(strict_types=1);

use App\Models\Translate;

it('can create a translation from the admin panel', function (): void {
    $translate = Translate::create([
        'name' => 'Riasztó, videó kamerás rendszer',
        'translated' => 'Alarm and video camera system',
        'lang' => 'EN',
    ]);

    expect($translate->exists)->toBeTrue()
        ->and(Translate::whereName('Riasztó, videó kamerás rendszer')->first()->translated)
        ->toBe('Alarm and video camera system');
});

it('can update an existing translation from the admin panel', function (): void {
    $translate = Translate::create([
        'name' => 'Riasztó, videó kamerás rendszer',
        'translated' => '',
        'lang' => 'EN',
    ]);

    $translate->update(['translated' => 'Alarm and video camera system']);

    expect($translate->fresh()->translated)->toBe('Alarm and video camera system');
});
