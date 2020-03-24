<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\Trim;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\Trim
     */
    public function testValidation()
    {
        $rule = new Trim();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', ' bar ', $validator);
        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
