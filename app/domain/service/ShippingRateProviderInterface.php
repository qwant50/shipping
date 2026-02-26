<?php

namespace app\domain\service;

use app\domain\entity\Address;
use app\domain\entity\Item;

interface ShippingRateProviderInterface
{
    public function getRate(Item $item, Address $destination): float;
}
