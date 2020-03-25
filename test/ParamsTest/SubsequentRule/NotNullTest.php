<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\NotNull;
use Params\ParamsValuesImpl;
use Params\Path;

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
        $validationResult = $rule1->process(
            Path::fromName('foo'),
            null,
            $validator
        );
        $this->assertNotNull($validationResult->getValidationProblems());

        $rule2 = new NotNull();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule2->process(
            Path::fromName('foo'),
            5,
            $validator
        );
        $this->assertEmpty($validationResult->getValidationProblems());
    }
}
