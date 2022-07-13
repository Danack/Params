<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\ExtractRule\GetArrayOfTypeOrNull;
use TypeSpecTest\BaseTestCase;
use TypeSpecTest\Integration\ReviewScore;
use TypeSpec\ProcessedValues;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpecTest\PropertyTypes\Quantity;

/**
 * @coversNothing
 */
class GetArrayOfParamOrNullTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfParamOrNull
     */
    public function testWorks()
    {
        $this->markTestSkipped("GetArrayOfParamOrNull not implemented yet.");

        $data = [
            [2, 5]
        ];

        $rule = new GetArrayOfParamOrNull(Quantity::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());

//        $this->assertCount(1, $result->getValue());
//        $item = ($result->getValue())[0];
//        $this->assertInstanceOf(ReviewScore::class, $item);
//        /** @var ReviewScore $item */
//        $this->assertSame(5, $item->getScore());
//        $this->assertSame('Hello world', $item->getComment());
    }
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParamOrNull
//     */
//    public function testWorksWhenNotSet()
//    {
//        $dataStorage = TestArrayDataStorage::fromArray([]);
//        $dataStorageAtItems = $dataStorage->moveKey('items');
//        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
//
//        $processedValues = new ProcessedValues();
//        $result = $rule->process(
//            $processedValues,
//            $dataStorageAtItems
//        );
//
//        $this->assertNull($result->getValue());
//        $this->assertEmpty($result->getValidationProblems());
//        $this->assertNoProblems($result);
//    }
//
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParamOrNull
//     */
//    public function testDescription()
//    {
//        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
//        $description = $this->applyRuleToDescription($rule);
//        $this->assertFalse($description->getRequired());
//    }
}
