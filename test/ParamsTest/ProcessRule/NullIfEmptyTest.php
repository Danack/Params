<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\NullIfEmpty;
use Params\ProcessedValuesImpl;

/**
 * @coversNothing
 */
class NullIfEmptyTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            ['pk_foobar', false],
            ['   .   ',   false],

            [null, true],
            ['', true],
            ['                    ', true],
        ];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\ProcessRule\NullIfEmpty
     */
    public function testValidationWorks($testValue, $shouldBeNull)
    {
        $rule = new NullIfEmpty();
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());


        if ($shouldBeNull === true) {
            $this->assertNull($validationResult->getValue());
        }
        else {
            $this->assertSame($testValue, $validationResult->getValue());
        }
    }
}
