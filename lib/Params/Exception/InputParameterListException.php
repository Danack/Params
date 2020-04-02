<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\Param;

class InputParameterListException extends ParamsException
{
    public static function notArray(string $classname): self
    {
        $message = sprintf(
            'Static function %s::getInputParameterList did not return an array. Must return %s[] ',
            $classname,
            Param::class
        );

        return new self($message);
    }

    public static function notInputParameter(int $index, string $classname): self
    {
        $message = sprintf(
            'Static function %s::getInputParameterList Must return %s[]. Item at index %d is wrong type.',
            $classname,
            Param::class,
            $index
        );

        return new self($message);
    }
}
