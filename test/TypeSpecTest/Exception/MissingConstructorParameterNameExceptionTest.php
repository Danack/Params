<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\MissingConstructorParameterNameException;

/**
 * @coversNothing
 */
class MissingConstructorParameterNameExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\MissingConstructorParameterNameException
     */
    public function testWorks()
    {
        $exception = MissingConstructorParameterNameException::missingParam(
            'class_name',
            'param_name'
        );

        $this->assertStringMatchesTemplateString(
            Messages::MISSING_PARAM_NAME,
            $exception->getMessage()
        );
        $this->assertStringContainsString('param_name', $exception->getMessage());
    }
}
