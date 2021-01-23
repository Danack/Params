<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfInt;
use Params\ProcessedValues;
use Params\ProcessRule\MaxIntValue;
use Params\InputStorage\ArrayInputStorage;

/**
 * @coversNothing
 */
class GetArrayOfIntTest extends BaseTestCase
{
    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $data = [5, 6, 7];

        $input = ['foo' => $data];

        $dataLocator = ArrayInputStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataLocator
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertSame($data, $result->getValue());
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $data = [5, 6, 7, 'banana'];


        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, ArrayInputStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/[3]',
            'Value must contain only digits.',
            $validationProblems
        );
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $data = [5, 6, 7, 5001];

        $rule = new GetArrayOfInt(
            new MaxIntValue(20)
        );
        $validator = new ProcessedValues();

        $result = $rule->process(
            $validator, ArrayInputStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getValidationProblems();

        $this->assertValidationProblem(
            '/[3]',
            'Value too large. Max allowed is 20',
            $problemMessages
        );
    }
}
