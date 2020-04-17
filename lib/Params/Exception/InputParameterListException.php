<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\InputParameter;

class InputParameterListException extends ParamsException
{
    public static function notArray(string $classname): self
    {
        $message = sprintf(
            'Static function %s::getInputParameterList did not return an array. Must return %s[] ',
            $classname,
            InputParameter::class
        );

        return new self($message);
    }

    public static function nonInputParameter(string $classname): self
    {
        $message = sprintf(
            'class [%s] does not implement interface [%s]. Cannot use it for',
            $classname,
            InputParameter::class,
        );

        return new self($message);
    }


    public static function foundNonInputParameter(int $index, string $classname): self
    {
        $message = sprintf(
            'Static function %s::getInputParameterList Must return array of %s. Item at index %d is wrong type.',
            $classname,
            InputParameter::class,
            $index
        );

        return new self($message);
    }
}
