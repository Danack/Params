<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\NullIfEmpty;
use Type\ProcessedValues;

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
     * @covers \Type\ProcessRule\NullIfEmpty
     */
    public function testValidationWorks($testValue, $shouldBeNull)
    {
        $rule = new NullIfEmpty();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);


        if ($shouldBeNull === true) {
            $this->assertNull($validationResult->getValue());
        }
        else {
            $this->assertSame($testValue, $validationResult->getValue());
        }
    }
}
