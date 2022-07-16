<?php

declare(strict_types=1);

namespace TypeSpecTest\PropertyInputTypeSpec;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\InputTypeSpec;
use TypeSpec\Messages;
use TypeSpec\ProcessedValues;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\PropertyInputTypeSpec\IntRangeOrDefault;
use TypeSpec\DataStorage\TestArrayDataStorage;
use function TypeSpec\processInputTypeSpec;

/**
 * @coversNothing
 */
class IntRangeOrDefaultTest extends BaseTestCase
{
    function provideTestWorks()
    {
        yield [0, 100, 0];
        yield [0, 100, 50];
        yield [0, 100, 100];
    }

    /**
     * @covers \TypeSpec\PropertyInputTypeSpec\IntRangeOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, $expected_value)
    {
        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $expected_value
        );

        $typeSpec = $intRange->getInputTypeSpec();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::createEmptyAtRoot();

        $validationProblems = processInputTypeSpec(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($expected_value, $result_value);
    }

    /**
     * @covers \TypeSpec\PropertyInputTypeSpec\IntRangeOrDefault
     */
    public function testWorksWithDefault()
    {
        $default_value = 50;
        $minimum = 0;
        $maximum = 100;

        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $default_value
        );
        $typeSpec = $intRange->getInputTypeSpec();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationProblems = processInputTypeSpec(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($default_value, $result_value);
    }


    function provideTestErrors()
    {
        yield [0, 100, -1, Messages::INT_TOO_SMALL];
        yield [0, 100, 101, Messages::INT_TOO_LARGE];
    }

    /**
     * @covers \TypeSpec\PropertyInputTypeSpec\IntRangeOrDefault
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, $expected_value, $expected_message)
    {
        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $expected_value
        );

        $typeSpec = $intRange->getInputTypeSpec();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $expected_value
        );

        $validationProblems = processInputTypeSpec(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_message,
            $validationProblems
        );
    }
}
