<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\DuplicatesParam;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\Enum;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class EnumTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['zoq', false, 'zoq'],
            ['Zebranky ', true, null],
            ['12345', false, '12345'],
            [12345, true, null]
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\Enum
     */
    public function testValidation($testValue, $expectError, $expectedValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        if ($expectError) {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
            return;
        }

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \Params\ProcessRule\Enum
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');

        $enumValues = ['zoq', 'fot', 'pik', '12345'];
        $rule = new Enum($enumValues);
        $rule->updateParamDescription($description);


        $this->assertSame($enumValues, $description->getEnumValues());
    }
}
