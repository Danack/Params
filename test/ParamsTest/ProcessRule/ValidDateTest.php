<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueInputStorageAye;
use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidDate;
use Params\ProcessedValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class ValidDateTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\ProcessRule\ValidDate
     * @group heisenbug
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataLocator
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }

    public function provideTestErrorsCases()
    {
        return [
            ['2pm on Tuesday'],
            ['Banana'],
        ];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \Params\ProcessRule\ValidDate
     */
    public function testValidationErrors($input)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $input, $processedValues, DataStorage::fromArraySetFirstValue([$input])
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
