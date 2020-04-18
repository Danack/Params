<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\ExtractRule\GetArrayOfTypeOrNull;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ReviewScore;
use VarMap\ArrayVarMap;
use Params\ProcessedValues;
use Params\Path;
use Params\DataLocator\DataStorage;

/**
 * @coversNothing
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \Params\ExtractRule\GetArrayOfTypeOrNull
     * @group
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
            DataStorage::fromArray($data)
        );

        $this->assertNoValidationProblems($result->getValidationProblems());

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $dataStorage = DataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);

        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            $dataStorageAtItems
        );

//        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getValidationProblems());
    }
}
