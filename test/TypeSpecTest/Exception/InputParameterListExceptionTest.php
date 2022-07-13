<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\TypeDefinitionException;

/**
 * @coversNothing
 */
class InputParameterListExceptionTest extends BaseTestCase
{
//    /**
//     * @covers \Params\Exception\InputParameterListException
//     */
//    public function testInputParameterListException_notArray()
//    {
//        $exception = InputParameterListException::notArray(self::class);
//        $this->assertStringRegExp(
//            Messages::GET_INPUT_PARAMETER_LIST_MUST_RETURN_ARRAY,
//            $exception->getMessage()
//        );
//        $this->assertStringContainsString(self::class, $exception->getMessage());
//        $this->assertSame(0, $exception->getCode());
//    }


    /**
     * @covers \TypeSpec\Exception\TypeDefinitionException
     */
    public function testInputParameterListException_foundNonInputParameter()
    {
        $position = 3;
        $classname = 'John';

        $exception = TypeDefinitionException::foundNonPropertyDefinition($position, $classname);


        $this->assertStringMatchesTemplateString(
            Messages::MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION,
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
