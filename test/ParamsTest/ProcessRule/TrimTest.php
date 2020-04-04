<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\Trim;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

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
        $validationResult = $rule->process(
            Path::fromName('foo'),
            ' bar ',
            $validator,
            SingleValueDataLocator::create(' bar ')
        );
        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
