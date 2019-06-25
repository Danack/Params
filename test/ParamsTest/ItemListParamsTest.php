<?php

declare(strict_types = 1);

namespace ParamsTest;

use VarMap\ArrayVarMap;
use Params\ValidationErrors;

/**
 * @group wip
 * @coversNothing
 */
class ItemListParamsTest extends BaseTestCase
{
    /**
     * @covers \ParamsTest\ItemListParams
     */
    public function testWorks()
    {
        $description = 'This is a test';

        $data = [
            'description' => $description,
            'items' => [
                ['foo' => 20, 'bar' => 'Hello'],
                ['foo' => 30, 'bar' => 'world'],
            ]
        ];

        /** @var ItemListParams $itemListParams */
        [$itemListParams, $error] = ItemListParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($error);

        $this->assertInstanceOf(ItemListParams::class, $itemListParams);
        $this->assertSame($description, $itemListParams->getDescription());

        $items = $itemListParams->getItems();
        $this->assertCount(2, $items);

        $item1 = $items[0];
        $this->assertSame(20, $item1->getFoo());
        $this->assertSame('Hello', $item1->getBar());

        $item2 = $items[1];
        $this->assertSame(30, $item2->getFoo());
        $this->assertSame('world', $item2->getBar());
    }


    /**
     * @covers \ParamsTest\ItemListParams
     */
    public function testItemsMissing()
    {
        $description = 'This is a test';

        $data = [
            'description' => $description,

        ];

        /** @var ItemListParams $itemListParams */
        [$itemListParams, $error] = ItemListParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($itemListParams);

        /** @var ValidationErrors $error */
        $validationProblems = $error->getValidationProblems();
        $this->assertCount(1, $validationProblems);

        $this->assertSame("Value not set for 'items'.", $validationProblems[0]);
    }
}
