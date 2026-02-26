<?php

namespace app\domain\entity;

final class Cart
{
    private ?Customer $customer = null;
    private ?Address $shippingAddress = null;

    /** @var Item[] */
    private array $items = [];

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    /** @return Item[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setShippingAddress(Address $address): void
    {
        $this->shippingAddress = $address;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }
}
