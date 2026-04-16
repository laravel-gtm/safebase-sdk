<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk;

use LaravelGtm\SafebaseSdk\Requests\GetAccountsRequest;
use LaravelGtm\SafebaseSdk\Responses\AccountsResponse;

class SafebaseSdk
{
    public function __construct(private readonly SafebaseConnector $connector) {}

    /**
     * @param  string|null  $baseUrl  Optional API base URL override.
     * @param  string|null  $token  API token (sent via the configured auth header).
     */
    public static function make(?string $baseUrl = null, ?string $token = null): self
    {
        return new self(new SafebaseConnector($baseUrl, $token));
    }

    /**
     * Get all accounts matching the given criteria.
     *
     * @see https://docs.safebase.io/reference/getaccounts
     */
    public function getAccounts(
        ?string $name = null,
        ?string $member = null,
        ?string $domain = null,
        ?string $sfdcAccountId = null,
        ?string $reviewStatus = null,
        ?string $sortField = null,
        ?string $sortDirection = null,
        ?int $pageNumber = null,
        ?int $pageSize = null,
    ): AccountsResponse {
        /** @var AccountsResponse */
        return $this->connector->send(new GetAccountsRequest(
            name: $name,
            member: $member,
            domain: $domain,
            sfdcAccountId: $sfdcAccountId,
            reviewStatus: $reviewStatus,
            sortField: $sortField,
            sortDirection: $sortDirection,
            pageNumber: $pageNumber,
            pageSize: $pageSize,
        ))->dtoOrFail();
    }
}
