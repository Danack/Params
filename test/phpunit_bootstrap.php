<?php

use Params\InputParameterList;
use Params\ExtractRule\GetString;
use Params\InputParameter;
use Params\ProcessRule\AlwaysErrorsRule;

/**
 * Used to convert to PHPUnits expected format.
 */
function stringToRegexp(string $string): string
{
    $string = preg_quote($string, '#');

    $replacements = [
        '%s' => '.*',   // strings can be empty, so *
        '%d' => '\d+',  // numbers can't be empty so +
    ];

    $string = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $string
    );

    return '#' . $string . '#iu';
}



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
