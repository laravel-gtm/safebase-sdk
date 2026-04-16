<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk\Responses;

readonly class Account
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public ?string $domain,
        public ?string $reviewStatus,
        public ?string $sfdcAccountId,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            name: (string) $data['name'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            domain: isset($data['domain']) ? (string) $data['domain'] : null,
            reviewStatus: isset($data['reviewStatus']) ? (string) $data['reviewStatus'] : null,
            sfdcAccountId: isset($data['sfdcAccountId']) ? (string) $data['sfdcAccountId'] : null,
        );
    }
}
