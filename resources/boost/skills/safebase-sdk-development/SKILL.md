---
name: safebase-sdk-development
description: Build features using the SafeBase trust center SDK — accounts, members, knowledge base, access requests, questionnaires, trust center updates, and products.
---

# safebase-sdk development

## When to use this skill

Use this skill when working on the `laravel-gtm/safebase-sdk` package — adding endpoints, building integrations with the SafeBase trust center API, writing tests, or extending existing resources.

## SDK entry point

```php
// Via Laravel container (recommended)
use LaravelGtm\SafebaseSdk\SafebaseSdk;

$sdk = app(SafebaseSdk::class);

// Standalone
$sdk = SafebaseSdk::make(
    baseUrl: 'https://app.safebase.io/api/ext/v1/rest',
    token: 'your-api-key',
);
```

The connector authenticates via the `X-Api-Key` header by default. The header name is configurable through the `SAFEBASE_AUTH_HEADER` env var or the `auth_header` config key.

## Resources and methods

### Accounts

Accounts represent the organizations that have access to your SafeBase trust center.

#### Get all accounts

```php
use LaravelGtm\SafebaseSdk\Requests\GetAccountsRequest;
use LaravelGtm\SafebaseSdk\Responses\AccountsResponse;
use LaravelGtm\SafebaseSdk\Responses\Account;

// Via the SDK class
$response = $sdk->getAccounts(
    name: 'Acme',
    domain: 'acme.com',
    reviewStatus: 'approved',
    sortField: 'lastSeen',
    sortDirection: 'desc',
    pageNumber: 1,
    pageSize: 25,
);

// $response->accounts — array<int, Account>
// $response->total    — int (total matching records)
// $response->pageNumber — int
// $response->pageSize   — int

foreach ($response->accounts as $account) {
    $account->id;             // string (UUID)
    $account->name;           // string
    $account->description;    // ?string
    $account->domain;         // ?string
    $account->reviewStatus;   // ?string
    $account->sfdcAccountId;  // ?string
}
```

**Available filter parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | `?string` | Search by account name |
| `member` | `?string` | Search by member email |
| `domain` | `?string` | Search by domain name |
| `sfdcAccountId` | `?string` | Salesforce Account ID |
| `reviewStatus` | `?string` | Filter by review status |
| `sortField` | `?string` | Sort field (e.g. `lastSeen`) |
| `sortDirection` | `?string` | `asc` or `desc` |
| `pageNumber` | `?int` | Page number (1–500) |
| `pageSize` | `?int` | Results per page (1–100) |

### Planned resources (not yet implemented)

The SafeBase API (`v1.19.2`) exposes additional resources that can be added following the same pattern as Accounts. All endpoints live under the base path `/api/ext/v1/rest`.

#### Account management (CRUD)

| Endpoint | Method | Path |
|----------|--------|------|
| Create account | `POST` | `/accounts` |
| Get account by ID | `GET` | `/accounts/{id}` |
| Edit account | `PATCH` | `/accounts/{id}` |
| Delete account | `DELETE` | `/accounts/{id}` |
| Get account page URL | `GET` | `/accounts/{id}/page-url` |

Edit account accepts: `name`, `description`, `sfdcAccountId`, `ndaProvider`, `ndaDocusign`, `accessExpiresAt`.

#### Account members

| Endpoint | Method | Path |
|----------|--------|------|
| Search members | `GET` | `/accounts/members` |
| Add members | `POST` | `/accounts/{id}/members` |
| Delete members | `DELETE` | `/accounts/{id}/members` |
| Notify members | `POST` | `/accounts/{id}/members/notify` |

Add members accepts an array of `{ firstName, lastName, email, message }` objects. Notify accepts `email` (string or array) and an optional `message`.

#### Knowledge base

| Endpoint | Method | Path |
|----------|--------|------|
| Search KB | `GET` | `/kb/search` |
| Create entry | `POST` | `/kb/entries` |
| Update entry | `PATCH` | `/kb/entries/{id}` |

Entries have `question`, `answer`, `comments`, and `accessLevel` (`public`, `private`, `internal`). Search accepts `query` (max 512 chars), `products` (comma-separated IDs), `pageNumber`, `pageSize`.

#### Access requests

| Endpoint | Method | Path |
|----------|--------|------|
| Get requests | `GET` | `/requests` |
| Approve request | `POST` | `/requests/{requestId}/approve` |
| Decline request | `POST` | `/requests/{requestId}/decline` |

Get requests accepts a `status` filter: `pending`, `approved`, `declined`. Approve accepts either `accountId` (existing) or `account` (new account object).

#### Organization

| Endpoint | Method | Path |
|----------|--------|------|
| Get organization | `GET` | `/organization` |
| Get NDA settings | `GET` | `/organization/nda-settings` |
| Get settings | `GET` | `/organization/settings` |
| Get current member | `GET` | `/organization/member/me` |

#### Portals

| Endpoint | Method | Path |
|----------|--------|------|
| Get portal products | `GET` | `/portals/default/products` |

#### Products

| Endpoint | Method | Path |
|----------|--------|------|
| Get all products | `GET` | `/products` |

#### Questionnaires

| Endpoint | Method | Path |
|----------|--------|------|
| Upload questionnaire | `POST` | `/questionnaires` |
| Get completed URL | `GET` | `/questionnaires/{id}` |

Upload is multipart: `qnr` (file — xlsx, pdf, docx, csv, json, txt), `accountId` (UUID, required), `productId` (optional), `dueDate` (ISO 8601, optional). The completed endpoint returns a signed URL valid for 60 minutes.

#### Trust center updates

| Endpoint | Method | Path |
|----------|--------|------|
| Create topic | `POST` | `/tcu/topics` |
| List topics | `GET` | `/tcu/topics` |
| Get topic | `GET` | `/tcu/topics/{topicId}` |
| Update topic | `PATCH` | `/tcu/topics/{topicId}` |
| Delete topic | `DELETE` | `/tcu/topics/{topicId}` |
| Create update | `POST` | `/tcu/topics/{topicId}/updates` |
| Get update | `GET` | `/tcu/topics/{topicId}/updates/{updateId}` |
| Edit update | `PATCH` | `/tcu/topics/{topicId}/updates/{updateId}` |
| Delete update | `DELETE` | `/tcu/topics/{topicId}/updates/{updateId}` |

Topics have `subject` (string), `category` (`compliance`, `vulnerabilities`, `incidents`, `subprocessors`, `general`), and `hidden` (bool). Updates have `message` (string) and `channels` (object with `statusPage`, `email.subscribers`, `email.audience`).

## Adding a new endpoint

1. Create a request class in `src/Requests/` extending `Saloon\Http\Request`:
   - Set `$method` (e.g. `Method::GET`, `Method::POST`)
   - Implement `resolveEndpoint()` returning the path (e.g. `/accounts/{id}`)
   - For GET requests, override `defaultQuery()` for query parameters
   - For POST/PATCH, implement `HasBody` + `HasJsonBody` and override `defaultBody()`
   - Implement `createDtoFromResponse()` to return a response DTO

2. Create response DTOs in `src/Responses/` as `readonly class` with `fromArray()`.

3. Add a public method on `SafebaseSdk` that sends the request and returns the DTO via `->dtoOrFail()`.

4. Write tests with `MockClient` / `MockResponse` (see testing section below).

## Rate limits

The connector enforces **60 requests per minute** via `Saloon\RateLimitPlugin`. The SafeBase API returns `429` when rate-limited; the connector backs off for 60 seconds on `429` responses.

In Laravel, the rate limit store uses `LaravelCacheStore` (bound via the service provider). Standalone usage falls back to `MemoryStore`.

## Testing with the SDK

```php
use LaravelGtm\SafebaseSdk\Requests\GetAccountsRequest;
use LaravelGtm\SafebaseSdk\Responses\Account;
use LaravelGtm\SafebaseSdk\Responses\AccountsResponse;
use LaravelGtm\SafebaseSdk\SafebaseConnector;
use LaravelGtm\SafebaseSdk\SafebaseSdk;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

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
    ->and($response->accounts[0])->toBeInstanceOf(Account::class)
    ->and($response->accounts[0]->name)->toBe('Acme Corp');

$mockClient->assertSent(GetAccountsRequest::class);
```

`tests/Pest.php` calls `Config::preventStrayRequests()` — any request without a mock will throw, preventing accidental live API calls.

## Conventions

- PHP 8.4+, `declare(strict_types=1)` in every file
- PHPStan level 8 — nullable types must be checked before access
- `laravel/pint` with Laravel preset: `composer lint` to check, `composer format` to fix
- Rule files: `.claude/rules/saloon.md`, `.claude/rules/php-package-phpstan.md`, `.claude/rules/laravel-package.md`
- All response DTOs are `readonly class` with typed constructor properties and `fromArray()`
- Use `dtoOrFail()` (not `dto()`) to ensure the response was successful before creating the DTO
