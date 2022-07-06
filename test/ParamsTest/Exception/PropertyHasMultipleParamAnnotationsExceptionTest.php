<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\Exception\PropertyHasMultipleParamAnnotationsException;

/**
 * @coversNothing
 */
class PropertyHasMultipleParamAnnotationsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Type\Exception\PropertyHasMultipleParamAnnotationsException
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
