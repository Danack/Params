<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\Trim;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \Params\SubsequentRule\Trim
     */
    public function testValidation()
    {
        $rule = new Trim();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', ' bar ', $validator);
        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
