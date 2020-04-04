<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use VarMap\ArrayVarMap;
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
                ['score' => 20, 'comment' => 'Hello'],
                ['score' => 30, 'comment' => 'world'],
            ]
        ];

        /** @var ItemListParams $itemListParams */
        [$itemListParams, $errors] = ItemListParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNoValidationProblems($errors);

        $this->assertInstanceOf(ItemListParams::class, $itemListParams);
        $this->assertSame($description, $itemListParams->getDescription());

        $items = $itemListParams->getItems();
        $this->assertCount(2, $items);

        $item1 = $items[0];
        $this->assertSame(20, $item1->getScore());
        $this->assertSame('Hello', $item1->getComment());

        $item2 = $items[1];
        $this->assertSame(30, $item2->getScore());
        $this->assertSame('world', $item2->getComment());
    }


    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testItemsMissing()
    {
        $this->markTestSkipped("needs fixing");
        return;

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
