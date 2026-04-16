<?php

declare(strict_types=1);

use LaravelGtm\SafebaseSdk\Tests\TestCase;
use Saloon\Config;

Config::preventStrayRequests();

uses(TestCase::class)->in('Feature');
