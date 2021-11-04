<?php

namespace Tests\Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Item;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Repositories\ItemRepository as ItemRepositoryInterface;
use Invoicing\Infrastructure\Repositories\Memory\ItemRepository;
use PHPUnit\Framework\TestCase;

class ItemRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_save_items(): ItemRepositoryInterface
    {
        $ids = [
            "1ec3d3a8-8690-6bb6-8e93-0242ac140002",
            "1ec3d3a8-8694-68ce-b3fc-0242ac140002",
            "1ec3d3a8-8694-6ef0-84ab-0242ac140002",
            "1ec3d3a8-8695-6472-8693-0242ac140002",
            "1ec3d3a8-8695-69cc-bb77-0242ac140002",
            "1ec3d3a8-8695-6f08-82b2-0242ac140002",
            "1ec3d3a8-8696-643a-8a1f-0242ac140002",
            "1ec3d3a8-8696-694e-8c0e-0242ac140002",
            "1ec3d3a8-8696-6e58-9959-0242ac140002",
            "1ec3d3a8-8697-636c-bffb-0242ac140002",
        ];

        /** @var Item[] $items */
        $items = [];
        foreach ($ids as $i => $id) {
            $items[] = Item::create(
                $id,
                sprintf("Item %s", $i + 1),
                rand(100, 200),
                Currency::create('USD', 'United States dollar')
            );
        }

        $itemRepository = new ItemRepository();
        foreach ($items as $item) {
            $itemRepository->save($item);
        }

        $this->checkStorage($items, $itemRepository);

        return $itemRepository;
    }

    /**
     * @test
     * @depends it_test_save_items
     */
    public function it_test_delete_items(ItemRepositoryInterface $itemRepository)
    {
        $items = $itemRepository->getAll();

        foreach ($items as $item) {
            $itemRepository->delete($item);
        }

        $this->checkStorage([], $itemRepository);
    }

    /**
     * @param Item[]         $items
     * @param ItemRepository $itemRepository
     */
    private function checkStorage(array $items, ItemRepository $itemRepository)
    {
        $storage = $itemRepository->getAll();

        $ids = array_unique(
            array_map(function (Item $item): string {
                return $item->getId()->getValue();
            }, $items)
        );

        $this->assertSameSize($ids, $storage);

        foreach ($items as $item) {
            $itemFound = $itemRepository->findById($item->getId()->getValue());

            $this->assertInstanceOf(Item::class, $itemFound);
            if (!is_null($itemFound)) {
                $this->assertEquals($item->getId(), $itemFound->getId());
            }
        }
    }
}
