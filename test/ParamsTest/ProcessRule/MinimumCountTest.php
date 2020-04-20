<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\FloatInput;
use Params\ProcessRule\MinimumCount;
use ParamsTest\BaseTestCase;
use Params\Exception\LogicException;
use Params\ProcessedValues;

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
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testWorks(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $values, $processedValues, $dataLocator
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
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
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testFails(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, DataStorage::fromArray([$values])
        );
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        $this->assertRegExp(
//            stringToRegexp(MinimumCount::ERROR_TOO_FEW_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            '/',
            Messages::ERROR_TOO_FEW_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
        new MinimumCount(-2);
    }

    /**
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MinimumCount(3);
        $this->expectException(LogicException::class);

        $processedValues = new ProcessedValues();
        $this->expectErrorMessageMatches(
            stringToRegexp(Messages::ERROR_WRONG_TYPE)
        );

        $rule->process(
            'a banana', $processedValues, DataStorage::fromArraySetFirstValue(['a banana'])
        );
    }


    /**
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testDescription()
    {
        $rule = new MinimumCount(3);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame(3, $description->getMinItems());
    }
}
