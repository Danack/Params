<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetInt;
use Params\ProcessedValues;
use Params\ExtractRule\GetKernelMatrixOrDefault;

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
     * @covers \Params\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks($input, $expected, $default)
    {
        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = ArrayInputStorage::fromSingleValue('foo', $input);

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expected);
    }
}
