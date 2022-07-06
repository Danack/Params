<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\Exception\MissingConstructorParameterNameException;

/**
 * @coversNothing
 */
class MissingConstructorParameterNameExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Type\Exception\MissingConstructorParameterNameException
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
