<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetArrayOfParam;
use TypeSpecTest\Integration\ReviewScore;
use TypeSpec\ProcessedValues;
use TypeSpecTest\Integration\SingleIntParams;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpecTest\PropertyTypes\Quantity;

/**
 * @coversNothing
 */
class GetArrayOfParamTest extends BaseTestCase
{
    /**
     * @group wip
     * @covers \TypeSpec\ExtractRule\GetArrayOfParam
     */
    public function testWorksForComplexType()
    {
        $this->markTestSkipped("not sure what this is meant to be testing.");

//        $data = [2, 5];
//
//        $rule = new GetArrayOfParam(Quantity::class);
//
//        $processedValues = new ProcessedValues();
//        $result = $rule->process(
//            $processedValues,
//            TestArrayDataStorage::fromArray($data)
//        );
//
//        $this->assertNoProblems($result);
//        $this->assertFalse($result->isFinalResult());
//
//        $this->assertCount(2, $result->getValue());
//
//        var_dump($result);

//        $item1 = ($result->getValue())[0];
//        $this->assertInstanceOf(ReviewScore::class, $item1);
//        /** @var ReviewScore $item1 */
//        $this->assertSame(5, $item1->getScore());
//        $this->assertSame($niceComment, $item1->getComment());
//
//
//        $item2 = ($result->getValue())[1];
//        $this->assertInstanceOf(ReviewScore::class, $item2);
//        /** @var ReviewScore $item2 */
//        $this->assertSame(2, $item2->getScore());
//        $this->assertSame($badComment, $item2->getComment());
    }


//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testWorksForComplexTypeForKey()
//    {
//        $niceComment = "This is great.";
//        $badComment = "Not so good.";
//
//        $data = [
//            'items' => [
//                ['score' => 5, 'comment' => $niceComment],
//                ['score' => 2, 'comment' => $badComment],
//            ]
//        ];
//
//        $dataStorage = TestArrayDataStorage::fromArray($data);
//        $dataStorage = $dataStorage->moveKey('items');
//
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $processedValues = new ProcessedValues();
//        $result = $rule->process(
//            $processedValues,
//            $dataStorage
//        );
//
//        $this->assertNoProblems($result);
//        $this->assertFalse($result->isFinalResult());
//
//        $this->assertCount(2, $result->getValue());
//
//        $item1 = ($result->getValue())[0];
//        $this->assertInstanceOf(ReviewScore::class, $item1);
//        /** @var ReviewScore $item1 */
//        $this->assertSame(5, $item1->getScore());
//        $this->assertSame($niceComment, $item1->getComment());
//
//
//        $item2 = ($result->getValue())[1];
//        $this->assertInstanceOf(ReviewScore::class, $item2);
//        /** @var ReviewScore $item2 */
//        $this->assertSame(2, $item2->getScore());
//        $this->assertSame($badComment, $item2->getComment());
//    }
//
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testWorksForIntType()
//    {
//        $data = [
//            ['limit' => 5]
//        ];
//
//        $rule = new GetArrayOfType(SingleIntParams::class);
//        $validator = new ProcessedValues();
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArray($data)
//        );
//
//        $this->assertFalse($result->isFinalResult());
//
//        $this->assertCount(1, $result->getValue());
//        $item = ($result->getValue())[0];
//        $this->assertInstanceOf(SingleIntParams::class, $item);
//
//        $this->assertNoProblems($result);
//        /** @var SingleIntParams $item */
//        $this->assertSame(5, $item->getLimit());
//    }
//
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testEmptyInputProducesEmptyOutput()
//    {
//        $data = [];
//
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $validator = new ProcessedValues();
//
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
//        );
//
//
//        $problems = $result->getValidationProblems();
//        $this->assertNoValidationProblems($problems);
//
//        $arrayOfType = $result->getValue();
//        $this->assertIsArray($arrayOfType);
//        $this->assertEmpty($arrayOfType);
//    }
//
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testScalarInsteadOfArrayErrors()
//    {
//
//        $data = [
//            'items' => 'a banana'
//        ];
//
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $validator = new ProcessedValues();
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
//        );
//        $this->assertTrue($result->isFinalResult());
//
//        $problems = $result->getValidationProblems();
//
//        $this->assertCount(1, $problems);
//        $this->assertValidationProblem(
//            '/items',
//            "Value must be an array.",
//            $problems
//        );
//
//        $this->assertNull($result->getValue());
//    }
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testScalarInsteadOfEntryArrayErrors()
//    {
//        $data = [
//            // wrong - should be ['limit' => 5]
//            5
//        ];
//
//        $rule = new GetArrayOfType(SingleIntParams::class);
//
//        $validator = new ProcessedValues();
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
//        );
//        $this->assertTrue($result->isFinalResult());
//        $validationProblems = $result->getValidationProblems();
//        $this->assertCount(1, $validationProblems);
//        $this->assertValidationProblemRegexp(
//            '/[0]',
//            //            Messages::ERROR_MESSAGE_ITEM_NOT_ARRAY,
//            Messages::ERROR_MESSAGE_NOT_ARRAY,
//            $validationProblems
//        );
//
//        $this->assertNull($result->getValue());
//    }
//
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testSingleError()
//    {
//        $data = [
//            'items' => [
//                ['score' => 5, 'comment' => false]
//            ],
//        ];
//
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $validator = new ProcessedValues();
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
//        );
//
//        $this->assertTrue($result->isFinalResult());
//        $this->assertNull($result->getValue());
//
//        $this->assertCount(1, $result->getValidationProblems());
//
//        $this->assertValidationProblemRegexp(
//            '/items[0]/comment',
//            Messages::STRING_TOO_SHORT,
//            $result->getValidationProblems()
//        );
//    }
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testMultipleErrors()
//    {
//        $data = [
//            ['score' => 5, 'comment' => 'foo'],
//            ['score' => 101, 'comment' => 'world']
//        ];
//
//        $validator = new ProcessedValues();
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $result = $rule->process(
//            $validator, TestArrayDataStorage::fromArray($data)
//        );
//
//        $this->assertTrue($result->isFinalResult());
//        $this->assertNull($result->getValue());
//
//        $validationProblems = $result->getValidationProblems();
//        $this->assertCount(2, $validationProblems);
//
//        $this->assertValidationProblemRegexp(
//            '/[0]/comment',
//            Messages::STRING_TOO_SHORT,
//            $validationProblems
//        );
//
//        $this->assertValidationProblem(
//            '/[1]/score',
//            "Value too large. Max allowed is 100",
//            $validationProblems
//        );
//    }
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testWorksForEmptyData()
//    {
//
//        $data = [];
//
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $processedValues = new ProcessedValues();
//        $result = $rule->process(
//            $processedValues,
//            TestArrayDataStorage::fromArray($data)
//        );
//
//        $this->assertNoProblems($result);
//        $this->assertFalse($result->isFinalResult());
//        $this->assertIsArray($result->getValue());
//        $this->assertEmpty($result->getValue());
//    }
//
//    /**
//     * @covers \Params\ExtractRule\GetArrayOfParam
//     */
//    public function testDescription()
//    {
//        $rule = new GetArrayOfType(ReviewScore::class);
//        $description = $this->applyRuleToDescription($rule);
//        // TODO - inspect description
//    }
}
