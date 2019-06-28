<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\NotNull;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \Params\SubsequentRule\NotNull
     */
    public function testValidation()
    {
        $rule1 = new NotNull();
        $validator = new ParamsValidator();
        $validationResult = $rule1->process('foo', null, $validator);
        $this->assertNotNull($validationResult->getProblemMessage());

        $rule2 = new NotNull();
        $validator = new ParamsValidator();
        $validationResult = $rule2->process('foo', 5, $validator);
        $this->assertNull($validationResult->getProblemMessage());
    }
}
