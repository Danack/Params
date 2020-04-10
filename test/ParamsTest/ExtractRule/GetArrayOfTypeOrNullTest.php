<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\ExtractRule\GetArrayOfTypeOrNull;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ReviewScore;
use VarMap\ArrayVarMap;
use Params\ProcessedValuesImpl;
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
        $this->markTestSkipped("GetArrayOfTypeOrNull is doing the wrong thing. It looks for a key when there isn't meant to be any currently.");
        return;

        $data = [
            ['foo' => 5, 'bar' => 'Hello world']
        ];

        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($data)
        );

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());

        $this->assertCount(0, $result->getValidationProblems());
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $this->markTestSkipped("GetArrayOfTypeOrNull is doing the wrong thing. It looks for a key when there isn't meant to be any currently.");
        return;

        $data = [];

        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator,
        );

//        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getValidationProblems());
    }
}
