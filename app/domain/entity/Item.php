<?php

namespace app\domain\entity;

final class Item
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $quantity,
        public readonly float $price,
    ) {}
}
