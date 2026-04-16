<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk\Requests;

use LaravelGtm\SafebaseSdk\Responses\AccountsResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * Get all accounts matching the given criteria.
 *
 * @see https://docs.safebase.io/reference/getaccounts
 */
class GetAccountsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $name = null,
        private readonly ?string $member = null,
        private readonly ?string $domain = null,
        private readonly ?string $sfdcAccountId = null,
        private readonly ?string $reviewStatus = null,
        private readonly ?string $sortField = null,
        private readonly ?string $sortDirection = null,
        private readonly ?int $pageNumber = null,
        private readonly ?int $pageSize = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/accounts';
    }

    /**
     * @return array<string, string|int>
     */
    protected function defaultQuery(): array
    {
        return array_filter([
            'name' => $this->name,
            'member' => $this->member,
            'domain' => $this->domain,
            'sfdcAccountId' => $this->sfdcAccountId,
            'reviewStatus' => $this->reviewStatus,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'pageNumber' => $this->pageNumber,
            'pageSize' => $this->pageSize,
        ], fn (mixed $value): bool => $value !== null);
    }

    public function createDtoFromResponse(Response $response): AccountsResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return AccountsResponse::fromArray($data);
    }
}
