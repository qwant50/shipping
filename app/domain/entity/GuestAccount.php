<?php

namespace app\domain\entity;

final class GuestAccount
{
    public function __construct(
        public readonly int $accountId,
        public readonly ?int $statusId,
        public readonly int $accountLimit,
        public readonly bool $allowCharges,
    ) {}

}
