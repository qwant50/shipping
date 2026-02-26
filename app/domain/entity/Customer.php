<?php

namespace app\domain\entity;

final class Customer
{
    /**
     * @param Address[] $addresses
     */
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly array $addresses,
    ) {}
}
