<?php

declare(strict_types=1);

namespace Tests;

use App\Services\FavoritesExcelExportService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public FavoritesExcelExportService $service;
}
