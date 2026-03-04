<?php

namespace app\domain\account;

use app\domain\money\Money;

final class Account
{
    private AccountId $id;
    private Money $balance;

    public function __construct(AccountId $id, Money $initialBalance)
    {
        $this->id = $id;
        $this->balance = $initialBalance;
    }

    public function debit(Money $amount): void
    {
        if ($amount->isNegative()) {
            throw new \DomainException('Cannot debit negative amount');
        }

        if ($this->balance->subtract($amount)->isNegative()) {
            throw new \DomainException('Insufficient funds');
        }

        $this->balance = $this->balance->subtract($amount);
    }

    public function credit(Money $amount): void
    {
        if ($amount->isNegative()) {
            throw new \DomainException('Cannot credit negative amount');
        }

        $this->balance = $this->balance->add($amount);
    }

    public function balance(): Money
    {
        return $this->balance;
    }
}