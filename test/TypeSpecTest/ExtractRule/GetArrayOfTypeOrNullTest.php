<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\ExtractRule\GetArrayOfTypeOrNull;
use TypeSpecTest\BaseTestCase;
use TypeSpecTest\Integration\ReviewScore;
use TypeSpec\ProcessedValues;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorks()
    {
        $data = [
            ['score' => 5, 'comment' => 'Hello world']
        ];

        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);

        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            $dataStorageAtItems
        );

        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getValidationProblems());
        $this->assertNoProblems($result);
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $this->assertFalse($description->getRequired());
    }
}
