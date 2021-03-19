<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class LogicExceptionTest extends BaseTestCase
{

    /**
     * @covers \Params\Exception\LogicException
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
