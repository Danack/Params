<?php

declare(strict_types = 1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\FirstRule\GetArrayOfInt;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\SubsequentRule\MaxIntValue;

/**
 * @coversNothing
 */
class GetArrayOfIntTest extends BaseTestCase
{
    /**
     * @covers  \Params\FirstRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $values = [5, 6, 7];

        $data = [
            'items' => $values,
        ];

        $rule = new GetArrayOfInt();
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertFalse($result->isFinalResult());
        $this->assertCount(0, $result->getProblemMessages());
        $this->assertSame($values, $result->getValue());
    }

    /**
     * @covers  \Params\FirstRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $values = [5, 6, 7, 'banana'];

        $data = [
            'items' => $values,
        ];

        $rule = new GetArrayOfInt();
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getProblemMessages();

        $this->assertCount(1, $problemMessages);
        $this->assertArrayHasKey('/items/3', $problemMessages);
        $this->assertSame('Value must contain only digits.', $problemMessages['/items/3']);
    }

    /**
     * @covers  \Params\FirstRule\GetArrayOfInt
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
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getProblemMessages();

        $this->assertCount(1, $problemMessages);
        $this->assertArrayHasKey('/items/3', $problemMessages);
        $this->assertSame('Value too large. Max allowed is 20', $problemMessages['/items/3']);
    }
}
