<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\NullIfEmpty;
use Params\ParamsValuesImpl;
use Params\Path;

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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );
        $this->assertEmpty($validationResult->getValidationProblems());


        if ($shouldBeNull === true) {
            $this->assertNull($validationResult->getValue());
        }
        else {
            $this->assertSame($testValue, $validationResult->getValue());
        }
    }
}
