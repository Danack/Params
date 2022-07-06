<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use Type\ExtractRule\GetArrayOfInt;
use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ExtractRule\GetInt;
use Type\ProcessedValues;
use Type\ExtractRule\GetKernelMatrixOrDefault;

/**
 * @coversNothing
 */
class GetKernelMatrixOrDefaultTest extends BaseTestCase
{
    const UNIT_MATRIX = [[1]];

    public function provideTestWorks()
    {
        $expected = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];
        yield [json_encode($expected), $expected, self::UNIT_MATRIX];
    }

    /**
     * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks($input, $expected, $default)
    {
        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $input);

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expected);
    }


    /**
     * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testMissingGivesDefault()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];


        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }




    /**
     * * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixRows()
    {
        $default = [
            [1, 2, 3],
            'John'
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_ROW
        );

        new GetKernelMatrixOrDefault($default);
    }

    /**
     * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixCell()
    {
        $default = [
            [1, 2, 3],
            [1, 2, 'John'],
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_CELL
        );

        new GetKernelMatrixOrDefault($default);
    }



    /**
     * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testDescription()
    {
        $default = [[1.5]];

        $rule = new GetKernelMatrixOrDefault($default);
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }



    /**
     * @covers \Type\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testBadInput_not_a_string()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', 123);

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE
        );

        $rule->process(
            $validator,
            $dataStorage
        );
    }
}
