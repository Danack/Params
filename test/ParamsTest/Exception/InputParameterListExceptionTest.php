<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\ValidationProblem;
use Params\Exception\InputParameterListException;

/**
 * @coversNothing
 */
class InputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \Params\Exception\InputParameterListException
     */
    public function testInputParameterListException_notArray()
    {
        $exception = InputParameterListException::notArray(self::class);
        $this->assertStringRegExp(
            Messages::GET_INPUT_PARAMETER_LIST_MUST_RETURN_ARRAY,
            $exception->getMessage()
        );
        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }


    /**
     * @covers \Params\Exception\InputParameterListException
     */
    public function testInputParameterListException_foundNonInputParameter()
    {
        $position = 3;
        $classname = 'John';

        $exception = InputParameterListException::foundNonInputParameter($position, $classname);


        $this->assertStringRegExp(
            Messages::MUST_RETURN_ARRAY_OF__INPUT_PARAMETER,
            $exception->getMessage()
        );
        $this->assertStringContainsString((string)$position, $exception->getMessage());
        $this->assertStringContainsString($classname, $exception->getMessage());
    }

//InvalidJsonPointer.php
//InvalidRulesException.php
//MissingClassException.php
//TypeNotInputParameterListException.php
}
