<?php

namespace app\application\account\transfer;

use app\domain\account\AccountRepository;
use app\domain\money\Currency;
use app\domain\money\Money;

final class TransferMoneyHandler
{
    public function __construct(
        private AccountRepository $accounts
    ) {}

    public function handle(TransferMoneyCommand $command): void
    {
        $from = $this->accounts->get($command->fromAccountId());
        $to   = $this->accounts->get($command->toAccountId());

        $money = Money::fromMinor($command->amount(), new Currency($command->currency()));

        $from->debit($money);
        $to->credit($money);

        $this->accounts->save($from);
        $this->accounts->save($to);
    }
}