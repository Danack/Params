<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\StartsWithString;
use Params\ParamsValuesImpl;
use Params\Path;

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
     * @covers \Params\ProcessRule\StartsWithString
     */
    public function testValidationWorks(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );
        $this->assertEmpty($validationResult->getValidationProblems());
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
     * @covers \Params\ProcessRule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );
        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
