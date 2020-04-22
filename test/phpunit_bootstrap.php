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

function json_decode_safe($json)
{
    if ($json === null) {
        throw new \Exception("Error decoding JSON: cannot decode null.");
    }

    $data = json_decode($json, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
    }

    throw new \Exception("Failed to decode json: " . json_last_error_msg());
//    $parser = new \Seld\JsonLint\JsonParser();
//    $parsingException = $parser->lint($json);
//
//    if ($parsingException !== null) {
//        throw $parsingException;
//    }
//
//    if ($data === null) {
//        throw new \Osf\Exception\JsonException("Error decoding JSON: null returned.");
//    }

//    throw new \Osf\Exception\JsonException("Error decoding JSON: " . json_last_error_msg());
}


function json_encode_safe($data, $options = 0): string
{
    $result = json_encode($data, $options);

    if ($result === false) {
        throw new \Exception("Failed to encode data as json: " . json_last_error_msg());
    }

    return $result;
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
