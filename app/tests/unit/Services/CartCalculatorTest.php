<?php

namespace app\tests\unit\Services;

use app\domain\entity\Address;
use app\domain\entity\Cart;
use app\domain\entity\Item;
use app\domain\service\CartCalculator;
use app\domain\service\ShippingRateProviderInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;

class CartCalculatorTest extends \PHPUnit\Framework\TestCase
{
    private Address $destination;
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->destination = new Address(
            $this->faker->streetAddress(),
            null,
            $this->faker->city(),
            $this->faker->stateAbbr(),
            $this->faker->postcode(),
        );
        var_dump($this->destination);
    }

    private function makeShipping(float $rate): ShippingRateProviderInterface
    {
        return new class($rate) implements ShippingRateProviderInterface {
            public function __construct(private readonly float $rate) {}
            public function getRate(Item $item, Address $destination): float
            {
                return $this->rate;
            }
        };
    }

    private function makeCart(float $shippingRate, Item ...$items): Cart
    {
        $cart = new Cart();
        $cart->setShippingAddress($this->destination);
        foreach ($items as $item) {
            $cart->addItem($item);
        }
        return $cart;
    }

    // -------------------------------------------------------------------------
    // itemCost
    // -------------------------------------------------------------------------

    public function testItemCostIsLineTotalPlusShippingPlusTax(): void
    {
        // price=10, qty=2 -> lineTotal=20, shipping=5, tax=20*0.07=1.40 -> 26.40
        $calc = new CartCalculator($this->makeShipping(5.00));
        $item = new Item(1, 'Widget', 2, 10.00);

        $cost = $calc->itemCost($item, $this->destination);

        $this->assertEqualsWithDelta(26.40, $cost, 0.001);
    }

    public function testItemCostTaxIsAppliedToLineTotalNotShipping(): void
    {
        // price=100, qty=1 -> lineTotal=100, shipping=50, tax=100*0.07=7 -> 157 (not 150*0.07)
        $calc = new CartCalculator($this->makeShipping(50.00));
        $item = new Item(1, 'Gadget', 1, 100.00);

        $cost = $calc->itemCost($item, $this->destination);

        $this->assertEqualsWithDelta(157.00, $cost, 0.001);
    }

    public function testItemCostWithZeroShipping(): void
    {
        // price=20, qty=3 -> lineTotal=60, shipping=0, tax=4.20 -> 64.20
        $calc = new CartCalculator($this->makeShipping(0.00));
        $item = new Item(1, 'Book', 3, 20.00);

        $cost = $calc->itemCost($item, $this->destination);

        $this->assertEqualsWithDelta(64.20, $cost, 0.001);
    }

    // -------------------------------------------------------------------------
    // subtotal
    // -------------------------------------------------------------------------

    public function testSubtotalReturnsSumOfLineTotals(): void
    {
        // item1: 10*2=20, item2: 5*3=15 -> 35
        $calc = new CartCalculator($this->makeShipping(0.00));
        $cart = $this->makeCart(0.00, new Item(1, 'A', 2, 10.00), new Item(2, 'B', 3, 5.00));

        $this->assertEqualsWithDelta(35.00, $calc->subtotal($cart), 0.001);
    }

    public function testSubtotalForEmptyCartIsZero(): void
    {
        $calc = new CartCalculator($this->makeShipping(5.00));
        $cart = $this->makeCart(5.00);

        $this->assertEqualsWithDelta(0.00, $calc->subtotal($cart), 0.001);
    }

    public function testSubtotalDoesNotIncludeShippingOrTax(): void
    {
        // price=100, qty=1 -> subtotal=100 (no 7% tax, no shipping)
        $calc = new CartCalculator($this->makeShipping(99.00));
        $cart = $this->makeCart(99.00, new Item(1, 'Pricey', 1, 100.00));

        $this->assertEqualsWithDelta(100.00, $calc->subtotal($cart), 0.001);
    }

    // -------------------------------------------------------------------------
    // total
    // -------------------------------------------------------------------------

    public function testTotalIsSumOfAllItemCosts(): void
    {
        // item1: 20 + 5 + 1.40 = 26.40
        // item2: 15 + 5 + 1.05 = 21.05
        // total = 47.45
        $calc = new CartCalculator($this->makeShipping(5.00));
        $cart = $this->makeCart(5.00, new Item(1, 'A', 2, 10.00), new Item(2, 'B', 3, 5.00));

        $this->assertEqualsWithDelta(47.45, $calc->total($cart), 0.001);
    }

    public function testTotalForEmptyCartIsZero(): void
    {
        $calc = new CartCalculator($this->makeShipping(5.00));
        $cart = $this->makeCart(5.00);

        $this->assertEqualsWithDelta(0.00, $calc->total($cart), 0.001);
    }

    public function testTotalExceedsSubtotalDueToShippingAndTax(): void
    {
        $calc = new CartCalculator($this->makeShipping(10.00));
        $cart = $this->makeCart(10.00, new Item(1, 'Thing', 1, 50.00));

        $this->assertGreaterThan($calc->subtotal($cart), $calc->total($cart));
    }

    public function testTotalPassesCartShippingAddressToProvider(): void
    {
        $capturedAddress = null;
        $provider = new class($capturedAddress) implements ShippingRateProviderInterface {
            public function __construct(private mixed &$captured) {}
            public function getRate(Item $item, Address $destination): float
            {
                $this->captured = $destination;
                return 0.00;
            }
        };

        $calc = new CartCalculator($provider);
        $cart = new Cart();
        $cart->setShippingAddress($this->destination);
        $cart->addItem(new Item(1, 'X', 1, 10.00));

        $calc->total($cart);

        $this->assertSame($this->destination, $capturedAddress);
    }
}
