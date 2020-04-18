<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\Messages;
use Params\ProcessRule\MinimumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaximumCount;
use Params\Exception\LogicException;
use Params\ProcessedValues;
use Params\DataLocator\DataStorage;

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
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, DataStorage::fromArray([$values])
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
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
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, DataStorage::fromArray([$values])
        );
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

//        $this->assertRegExp(
//            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            '/',
            Messages::ERROR_TOO_MANY_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MaximumCount(3);
        $this->expectException(LogicException::class);

        $processedValues = new ProcessedValues();
        $this->expectErrorMessageMatches(
            stringToRegexp(Messages::ERROR_WRONG_TYPE_VARIANT_1)
        );

        $dataLocator = DataStorage::fromArraySetFirstValue([]);

        $rule->process(
            'a banana', $processedValues, $dataLocator
        );
    }
}
