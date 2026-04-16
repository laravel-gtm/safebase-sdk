<?php

declare(strict_types=1);

use LaravelGtm\SafebaseSdk\SafebaseConnector;
use Saloon\Http\Auth\HeaderAuthenticator;

it('resolves custom base urls without trailing slash', function (): void {
    $connector = new SafebaseConnector('https://example.test/', null);

    expect($connector->resolveBaseUrl())->toBe('https://example.test');
});

it('defaults to the safebase api base url when no base url is set', function (): void {
    $connector = new SafebaseConnector;

    expect($connector->resolveBaseUrl())->toBe('https://app.safebase.io/api/ext/v1/rest');
});

it('returns null default auth when token is missing', function (): void {
    $connector = new SafebaseConnector(null, null);
    $method = new ReflectionMethod(SafebaseConnector::class, 'defaultAuth');

    expect($method->invoke($connector))->toBeNull();
});

it('builds header auth when token is provided', function (): void {
    $connector = new SafebaseConnector(null, 'test-token');
    $method = new ReflectionMethod(SafebaseConnector::class, 'defaultAuth');

    expect($method->invoke($connector))->toBeInstanceOf(HeaderAuthenticator::class);
});

it('uses custom auth header name', function (): void {
    $connector = new SafebaseConnector(null, 'secret', 'Authorization');
    $method = new ReflectionMethod(SafebaseConnector::class, 'defaultAuth');
    $auth = $method->invoke($connector);

    expect($auth)->toBeInstanceOf(HeaderAuthenticator::class);
});
