<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\NullIfEmpty;

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

            ['', true],
            ['                    ', true],
        ];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\Rule\StartsWithString
     */
    public function testValidationWorks($testValue, $shouldBeNull)
    {
        $validator = new NullIfEmpty();

        $validationResult = $validator('foo', $testValue);
        $this->assertNull($validationResult->getProblemMessage());


        if ($shouldBeNull === true) {
            $this->assertNull($validationResult->getValue());
        }
        else {
            $this->assertSame($testValue, $validationResult->getValue());
        }
    }
}
