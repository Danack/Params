<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ParamsValuesImpl;
use VarMap\ArrayVarMap;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\AlwaysEndsRule
     */
    public function testUnknownFilterErrors()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $result = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            $dataLocator
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
        $this->assertCount(0, $result->getValidationProblems());
    }
}
