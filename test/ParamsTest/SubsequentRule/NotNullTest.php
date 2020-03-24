<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\NotNull;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\NotNull
     */
    public function testValidation()
    {
        $rule1 = new NotNull();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule1->process('foo', null, $validator);
        $this->assertNotNull($validationResult->getValidationProblems());

        $rule2 = new NotNull();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule2->process('foo', 5, $validator);
        $this->assertEmpty($validationResult->getValidationProblems());
    }
}
