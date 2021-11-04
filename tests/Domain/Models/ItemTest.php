<?php

namespace Tests\Invoicing\Domain\Models;

use Invoicing\Domain\Models\Item;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Money;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_item(): Item
    {
        $id    = new Uuid('1ec3cb5a-68c6-6456-bc6d-0242ac140002');
        $name  = new Name("Item Name");
        $price = new Money(1000, Currency::create('USD', 'United States dollar'));

        $item = new Item($id, $name, $price);

        $this->assertSame($id, $item->getId());
        $this->assertSame($name, $item->getname());
        $this->assertSame($price, $item->getprice());

        return $item;
    }

    /**
     * @test
     * @depends it_test_valid_item
     */
    public function it_test_valid_item_with_factory(Item $item)
    {
        $itemWithFactory = Item::create(
            $item->getId()->getValue(),
            $item->getName()->getValue(),
            $item->getPrice()->getAmount(),
            $item->getPrice()->getCurrency()
        );

        $this->assertEquals($item->getId(), $itemWithFactory->getId());
        $this->assertEquals($item->getName(), $itemWithFactory->getName());
        $this->assertEquals($item->getPrice(), $itemWithFactory->getPrice());
    }

    /**
     * @test
     * @depends it_test_valid_item
     */
    public function it_test_change_name(Item $item)
    {
        $newName = 'A new item name';
        $item->changeName($newName);

        $this->assertSame($newName, $item->getName()->getValue());
    }

    /**
     * @test
     * @depends it_test_valid_item
     */
    public function it_test_change_price(Item $item)
    {
        $newPrice = new Money(3000, Currency::create('USD', 'United States dollar'));

        $item->changePrice($newPrice);

        $this->assertEquals($newPrice, $item->getPrice());
    }
}
