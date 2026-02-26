<?php

namespace app\domain\service;

use app\domain\entity\Address;
use app\domain\entity\Cart;
use app\domain\entity\Item;

class CartCalculator
{
    private const TAX_RATE = 0.07;

    public function __construct(
        private readonly ShippingRateProviderInterface $shippingRates,
    ) {}

    /**
     * Cost of a single item line: (price × quantity) + shipping + tax.
     */
    public function itemCost(Item $item, Address $destination): float
    {
        $lineTotal = $item->price * $item->quantity;
        $shipping  = $this->shippingRates->getRate($item, $destination);
        $tax       = $lineTotal * self::TAX_RATE;

        return $lineTotal + $shipping + $tax;
    }

    /**
     * Sum of (price × quantity) for all items — before shipping and tax.
     */
    public function subtotal(Cart $cart): float
    {
        return array_sum(array_map(
            static fn(Item $item) => $item->price * $item->quantity,
            $cart->getItems(),
        ));
    }

    /**
     * Sum of itemCost() for every line — shipping and tax included.
     */
    public function total(Cart $cart): float
    {
        $destination = $cart->getShippingAddress();

        return array_sum(array_map(
            fn(Item $item) => $this->itemCost($item, $destination),
            $cart->getItems(),
        ));
    }
}
