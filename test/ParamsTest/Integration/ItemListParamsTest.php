<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use VarMap\ArrayVarMap;
use Params\ValidationErrors;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class ItemListParamsTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetArrayOfType
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
        [$itemListParams, $errors] = ItemListParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertEmpty($errors);

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
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testItemsMissing()
    {
        $description = 'This is a test';

        $data = [
            'description' => $description,
        ];

        /** @var ItemListParams $itemListParams */
        [$itemListParams, $validationProblems] = ItemListParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($itemListParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            'items',
            "Value must be set.",
            $validationProblems
        );

//        /** @var \Params\ValidationProblem $firstProblem */
//        $firstProblem = $validationProblems[0];
//
//        $expectedKey = 'items';
//        $this->assertSame($expectedKey, $firstProblem->getPath());
//        $this->assertSame("Value must be set.", $firstProblem->getProblemMessage());
    }
}
