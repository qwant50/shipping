<?php

namespace app\application\account\transfer;

final class TransferMoneyCommand
{
    public function __construct(
        private string $fromAccountId,
        private string $toAccountId,
        private int $amount,        // minor units (e.g. cents)
        private string $currency,   // ISO code
        private string $reference   // external idempotency key
    ) {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        if ($fromAccountId === $toAccountId) {
            throw new \InvalidArgumentException('Cannot transfer to same account');
        }
    }

    public function fromAccountId(): string
    {
        return $this->fromAccountId;
    }

    public function toAccountId(): string
    {
        return $this->toAccountId;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function reference(): string
    {
        return $this->reference;
    }
}