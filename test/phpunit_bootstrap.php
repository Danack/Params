<?php

use Params\InputParameterList;
use Params\ExtractRule\GetString;
use Params\InputParameter;
use Params\ProcessRule\AlwaysErrorsRule;

class TestObject
{
    private string $foo;
    private int $bar;

    public function __construct(
        string $foo,
        int $bar
    ) {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): int
    {
        return $this->bar;
    }
}

class DoesNotImplementInputParameterList
{
}


class ReturnsBadInputParameterList implements InputParameterList
{
    public static function getInputParameterList(): array
    {
        return [
            // Wrong type
            new StdClass()
        ];
    }
}

class TestParams implements InputParameterList
{
    private string $name;

    /**
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'name',
                new GetString(),
            )
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}


class AlwaysErrorsParams implements InputParameterList
{
    public const ERROR_MESSAGE = 'Forced error';

    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'foo',
                new GetString(),
            ),
            new InputParameter(
                'bar',
                new GetString(),
                new AlwaysErrorsRule(self::ERROR_MESSAGE)
            )
        ];
    }
}
