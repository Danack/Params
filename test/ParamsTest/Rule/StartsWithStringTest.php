<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\StartsWithString;

/**
 * @coversNothing
 */
class StartsWithStringTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            ['pk_', 'pk_foobar'],
            ['_', '_foobar'],
        ];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\Rule\StartsWithString
     */
    public function testValidationWorks(string $prefix, $testValue)
    {
        $validator = new StartsWithString($prefix);

        $validationResult = $validator('foo', $testValue);
        $this->assertNull($validationResult->getProblemMessage());
        $this->assertSame($validationResult->getValue(), $testValue);
    }

    public function provideTestFailsCases()
    {
        return [
            ['pk_', 'dk_foobar'],
            ['_', 'f_oobar', true],
        ];
    }

    /**
     * @dataProvider provideTestFailsCases
     * @covers \Params\Rule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $validator = new StartsWithString($prefix);

        $validationResult = $validator('foo', $testValue);
        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
