<?php

declare(strict_types = 1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfInt;
use VarMap\ArrayVarMap;
use Params\ParamsValuesImpl;
use Params\ProcessRule\MaxIntValue;

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
        $values = [5, 6, 7];

        $data = [
            'items' => $values,
        ];

        $rule = new GetArrayOfInt();
        $validator = new ParamsValuesImpl();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertFalse($result->isFinalResult());
        $this->assertCount(0, $result->getValidationProblems());
        $this->assertSame($values, $result->getValue());
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $values = [5, 6, 7, 'banana'];

        $data = [
            'items' => $values,
        ];

        $rule = new GetArrayOfInt();
        $validator = new ParamsValuesImpl();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        /** @var \Params\ValidationProblem $firstProblem */
        $firstProblem = $validationProblems[0];

        $this->assertSame('/items/3', $firstProblem->getIdentifier());
        $this->assertSame('Value must contain only digits.', $firstProblem->getProblemMessage());
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $values = [5, 6, 7, 5001];

        $data = [
            'items' => $values,
        ];

        $rule = new GetArrayOfInt(
            new MaxIntValue(20)
        );
        $validator = new ParamsValuesImpl();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getValidationProblems();

        $this->assertCount(1, $problemMessages);
        $this->assertArrayHasKey('/items/3', $problemMessages);
        $this->assertSame('Value too large. Max allowed is 20', $problemMessages['/items/3']);
    }
}
