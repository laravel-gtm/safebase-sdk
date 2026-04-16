## SafeBase SDK (`laravel-gtm/safebase-sdk`)

A Laravel-ready PHP SDK for the [SafeBase](https://safebase.io) trust center API, built with Saloon 4.

### Setup

Add these environment variables to your `.env`:

```
SAFEBASE_BASE_URL=https://app.safebase.io/api/ext/v1/rest
SAFEBASE_TOKEN=your-api-key
SAFEBASE_AUTH_HEADER=X-Api-Key
```

Publish the config file:

```bash
php artisan vendor:publish --tag=safebase-sdk-config
```

### Usage

Inject the SDK via the Laravel container or create a standalone instance:

```php
use LaravelGtm\SafebaseSdk\SafebaseSdk;

// Laravel (auto-configured from config)
$sdk = app(SafebaseSdk::class);

// Standalone
$sdk = SafebaseSdk::make(
    baseUrl: 'https://app.safebase.io/api/ext/v1/rest',
    token: 'your-api-key',
);
```

### Request pattern

@verbatim
<code-snippet name="Get accounts with filters" lang="php">
use LaravelGtm\SafebaseSdk\SafebaseSdk;

$sdk = app(SafebaseSdk::class);

$response = $sdk->getAccounts(
    name: 'Acme',
    reviewStatus: 'approved',
    sortField: 'lastSeen',
    sortDirection: 'desc',
    pageNumber: 1,
    pageSize: 25,
);

foreach ($response->accounts as $account) {
    echo "{$account->name} ({$account->domain})\n";
}
</code-snippet>
@endverbatim

### Testing

@verbatim
<code-snippet name="Mock SafeBase API calls in tests" lang="php">
use LaravelGtm\SafebaseSdk\Requests\GetAccountsRequest;
use LaravelGtm\SafebaseSdk\SafebaseConnector;
use LaravelGtm\SafebaseSdk\SafebaseSdk;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$connector = new SafebaseConnector('https://example.test/api/ext/v1/rest', 'test-token');
$connector->withMockClient(new MockClient([
    GetAccountsRequest::class => MockResponse::make([
        'data' => [
            ['id' => 'abc-123', 'name' => 'Acme Corp', 'domain' => 'acme.com'],
        ],
        'total' => 1,
        'pageNumber' => 1,
        'pageSize' => 10,
    ], 200),
]));

$sdk = new SafebaseSdk($connector);
$response = $sdk->getAccounts(name: 'Acme');

expect($response->accounts)->toHaveCount(1);
</code-snippet>
@endverbatim

### Important notes

- **Authentication**: API key sent via `X-Api-Key` header (configurable via `SAFEBASE_AUTH_HEADER`)
- **Rate limits**: 60 requests per minute; the connector backs off for 60 seconds on `429` responses
- **Timeouts**: 10s connect, 30s request
- **Error handling**: The connector uses `AlwaysThrowOnErrors` — failed requests throw automatically
- **Stray requests**: `Config::preventStrayRequests()` is enabled in tests; every request needs a mock
