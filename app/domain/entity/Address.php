<?php

namespace app\domain\entity;

final class Address
{
    public function __construct(
        public readonly string $line1,
        public readonly ?string $line2,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zip,
    ) {}
}
