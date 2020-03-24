<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ProcessRule\MinimumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaximumCount;
use Params\Exception\LogicException;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
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
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testWorks(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertEmpty($validationResult->getValidationProblems());
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
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testFails(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

//        $this->assertRegExp(
//            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            'foo',
            MaximumCount::ERROR_TOO_MANY_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(MaximumCount::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MaximumCount(3);
        $this->expectException(LogicException::class);

        $validator = new ParamsValuesImpl();
        $this->expectErrorMessageMatches(
            stringToRegexp(MaximumCount::ERROR_WRONG_TYPE)
        );

        $rule->process('foo', 'a banana', $validator);
    }
}
