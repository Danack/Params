<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MaximumCount;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group wip
 */
class MaximumCountTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [3, []], // 3 <= 3
            [3, [1, 2, 3]], // 3 <= 3
            [4, [1, 2, 3]], // 3 <= 4
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\Rule\MaximumCount
     */
    public function testWorks(int $maximumCount, $values)
    {
        $validator = new MaximumCount($maximumCount);
        $validationResult = $validator('foo', $values);
        $this->assertNull($validationResult->getProblemMessage());
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public function provideFailsCases()
    {
        return [
            [0, [1, 2, 3]], // 3 > 0
            [3, [1, 2, 3, 4]], // 4 > 3
        ];
    }

    /**
     * @dataProvider provideFailsCases
     * @covers \Params\Rule\MaximumCount
     */
    public function testFails(int $maximumCount, $values)
    {
        $validator = new MaximumCount($maximumCount);
        $validationResult = $validator('foo', $values);
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

        $this->assertRegExp(
            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
            $validationResult->getProblemMessage()
        );

    }

    /**
     * @covers \Params\Rule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(MaximumCount::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \Params\Rule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $validator = new MaximumCount(3);
        $this->expectException(LogicException::class);

        $this->expectExceptionMessageRegExp(
            stringToRegexp(MaximumCount::ERROR_WRONG_TYPE)
        );

        $validator('foo', 'a banana');
    }
}
