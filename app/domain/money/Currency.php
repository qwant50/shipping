<?php

namespace app\domain\money;

final class Currency
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = strtoupper($code);
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function fractionDigits(): int
    {
        return 100; // 2 decimal places (EUR, USD)
    }

    public function code(): string
    {
        return $this->code;
    }
}