<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException;

/**
 * @coversNothing
 */
class PropertyHasMultipleParamAnnotationsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException
     */
    public function testWorks()
    {
        $exception = PropertyHasMultipleInputTypeSpecAnnotationsException::create(
            'class_name',
            'param_name'
        );

        $this->assertStringMatchesTemplateString(
            Messages::PROPERTY_MULTIPLE_INPUT_TYPE_SPEC,
            $exception->getMessage()
        );
        $this->assertStringContainsString('param_name', $exception->getMessage());
    }
}
