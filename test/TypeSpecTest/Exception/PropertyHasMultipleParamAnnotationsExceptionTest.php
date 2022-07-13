<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\PropertyHasMultipleParamAnnotationsException;

/**
 * @coversNothing
 */
class PropertyHasMultipleParamAnnotationsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\PropertyHasMultipleParamAnnotationsException
     */
    public function testWorks()
    {
        $exception = PropertyHasMultipleParamAnnotationsException::create(
            'class_name',
            'param_name'
        );

        $this->assertStringMatchesTemplateString(
            Messages::PROPERTY_MULTIPLE_PARAMS,
            $exception->getMessage()
        );
        $this->assertStringContainsString('param_name', $exception->getMessage());
    }
}
