<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\SubsequentRule\MinimumCount;
use ParamsTest\BaseTestCase;
use Params\Exception\LogicException;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MinimumCountTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [0, [1]], // 0 <= 1
            [1, [1]], // 1 <= 1
            [2, [1, 2]], // 2 <= 2
            [2, [1, 2, 3, 4, 5]], // 2 <= 5
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\SubsequentRule\MinimumCount
     */
    public function testWorks(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public function provideFailsCases()
    {
        return [
            [1, []], // 3 > 0
            [3, [1, 2]], // 4 > 3
            [50, [1, 2]], // 4 > 3
        ];
    }

    /**
     * @dataProvider provideFailsCases
     * @covers \Params\SubsequentRule\MinimumCount
     */
    public function testFails(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

        $this->assertRegExp(
            stringToRegexp(MinimumCount::ERROR_TOO_FEW_ELEMENTS),
            $validationResult->getProblemMessages()['/foo']
        );
    }

    /**
     * @covers \Params\SubsequentRule\MinimumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(MinimumCount::ERROR_MINIMUM_COUNT_MINIMUM);
        new MinimumCount(-2);
    }

    /**
     * @covers \Params\SubsequentRule\MinimumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MinimumCount(3);
        $this->expectException(LogicException::class);

        $validator = new ParamsValidator();
        $this->expectExceptionMessageRegExp(
            stringToRegexp(MinimumCount::ERROR_WRONG_TYPE)
        );

        $rule->process('foo', 'a banana', $validator);
    }
}
