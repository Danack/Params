<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\InputParameter;
use Params\Messages;

class InputParameterListException extends ParamsException
{
//    public static function notArray(string $classname): self
//    {
//        $message = sprintf(
//            Messages::GET_INPUT_PARAMETER_LIST_MUST_RETURN_ARRAY,
//            $classname,
//            InputParameter::class
//        );
//
//        return new self($message);
//    }


    public static function foundNonInputParameter(int $index, string $classname): self
    {
        $message = sprintf(
            Messages::MUST_RETURN_ARRAY_OF__INPUT_PARAMETER,
            $classname,
            InputParameter::class,
            $index
        );

        return new self($message);
    }
}
