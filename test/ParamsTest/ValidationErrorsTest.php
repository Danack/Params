<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\ValidationErrors;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class ValidationErrorsTest extends BaseTestCase
{
    /**
     * @covers \Params\ValidationErrors
     */
    public function testBasic()
    {
        $errorStrings = [
            'error 1',
            'error 2',
            'error 3',
        ];

        $validationErrors = new ValidationErrors($errorStrings);

        $iteratedErrors = [];

        foreach ($validationErrors as $error) {
            $iteratedErrors[] = $error;
        }

        $this->assertEquals($errorStrings, $iteratedErrors);
        $this->assertSame($errorStrings, $validationErrors->getValidationProblems());
        $this->assertSame('error 1,error 2,error 3', $validationErrors->__toString());
    }
}
