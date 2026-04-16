<?php

declare(strict_types=1);

use LaravelGtm\SafebaseSdk\Requests\GetAccountsRequest;
use LaravelGtm\SafebaseSdk\Responses\Account;
use LaravelGtm\SafebaseSdk\Responses\AccountsResponse;
use LaravelGtm\SafebaseSdk\SafebaseConnector;
use LaravelGtm\SafebaseSdk\SafebaseSdk;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('returns an accounts response from getAccounts', function (): void {
    $connector = new SafebaseConnector('https://example.test/api/ext/v1/rest', 'test-token');
    $mockClient = new MockClient([
        GetAccountsRequest::class => MockResponse::make([
            'data' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'name' => 'Acme Corp',
                    'description' => 'Enterprise customer',
                    'domain' => 'acme.com',
                    'reviewStatus' => 'approved',
                    'sfdcAccountId' => '001ABC123',
                ],
            ],
            'total' => 1,
            'pageNumber' => 1,
            'pageSize' => 10,
        ], 200),
    ]);
    $connector->withMockClient($mockClient);

    $sdk = new SafebaseSdk($connector);
    $response = $sdk->getAccounts(name: 'Acme');

    expect($response)->toBeInstanceOf(AccountsResponse::class)
        ->and($response->total)->toBe(1)
        ->and($response->pageNumber)->toBe(1)
        ->and($response->pageSize)->toBe(10)
        ->and($response->accounts)->toHaveCount(1)
        ->and($response->accounts[0])->toBeInstanceOf(Account::class)
        ->and($response->accounts[0]->id)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($response->accounts[0]->name)->toBe('Acme Corp')
        ->and($response->accounts[0]->domain)->toBe('acme.com');

    $mockClient->assertSent(GetAccountsRequest::class);
});

it('returns an empty accounts response', function (): void {
    $connector = new SafebaseConnector('https://example.test/api/ext/v1/rest', 'test-token');
    $mockClient = new MockClient([
        GetAccountsRequest::class => MockResponse::make([
            'data' => [],
            'total' => 0,
            'pageNumber' => 1,
            'pageSize' => 10,
        ], 200),
    ]);
    $connector->withMockClient($mockClient);

    $sdk = new SafebaseSdk($connector);
    $response = $sdk->getAccounts();

    expect($response->accounts)->toBeEmpty()
        ->and($response->total)->toBe(0);

    $mockClient->assertSent(GetAccountsRequest::class);
});
