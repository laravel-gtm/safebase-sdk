<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk\Tests;

use LaravelGtm\SafebaseSdk\Laravel\SafebaseServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [SafebaseServiceProvider::class];
    }
}
