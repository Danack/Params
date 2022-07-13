<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\LogicException;

/**
 * @coversNothing
 */
class LogicExceptionTest extends BaseTestCase
{

    /**
     * @covers \TypeSpec\Exception\LogicException
     */
    public function testWorks()
    {
        $exception = LogicException::keysMustBeStrings();
        $this->assertStringMatchesTemplateString(
            LogicException::ONLY_KEYS,
            $exception->getMessage()
        );

        $exception = LogicException::onlyInputParameters('foo');
        $this->assertStringMatchesTemplateString(
            LogicException::NOT_VALIDATION_PROBLEM,
            $exception->getMessage()
        );

        $exception = LogicException::keysMustBeIntegers();
        $this->assertStringMatchesTemplateString(
            LogicException::ONLY_INT_KEYS,
            $exception->getMessage()
        );

        $exception = LogicException::missingValue('foo');
        $this->assertStringMatchesTemplateString(
            LogicException::MISSING_VALUE,
            $exception->getMessage()
        );
    }
}
