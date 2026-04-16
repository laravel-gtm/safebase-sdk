<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk\Responses;

readonly class AccountsResponse
{
    /**
     * @param  array<int, Account>  $accounts
     */
    public function __construct(
        public array $accounts,
        public int $total,
        public int $pageNumber,
        public int $pageSize,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = $data['data'] ?? [];

        return new self(
            accounts: array_map(Account::fromArray(...), $items),
            total: (int) ($data['total'] ?? 0),
            pageNumber: (int) ($data['pageNumber'] ?? 1),
            pageSize: (int) ($data['pageSize'] ?? count($items)),
        );
    }
}
