<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\NotNull;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

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
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule1->process(
            Path::fromName('foo'),
            null,
            $validator,
            $dataLocator
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());

        $rule2 = new NotNull();
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule2->process(
            Path::fromName('foo'),
            5,
            $validator,
            $dataLocator
        );
        $this->assertEmpty($validationResult->getValidationProblems());
    }
}
