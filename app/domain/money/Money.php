<?php

namespace app\domain\money;

final class Money
{
    private int $amount; // stored in minor units (cents)
    private Currency $currency;

    private function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromMinor(int $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    public static function fromDecimal(string $amount, Currency $currency): self
    {
        // "10.50" -> 1050
        $minor = (int) bcmul($amount, (string) $currency->fractionDigits());
        return new self($minor, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(
            $this->amount + $other->amount,
            $this->currency
        );
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(
            $this->amount - $other->amount,
            $this->currency
        );
    }

    public function multiply(int $multiplier): self
    {
        return new self(
            $this->amount * $multiplier,
            $this->currency
        );
    }

    public function greaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    private function assertSameCurrency(self $other): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw new \DomainException('Currency mismatch');
        }
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }
}