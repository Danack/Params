<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

/**
 * Thrown when someone calls getValue DataStorage object
 */
class InvalidLocationException extends ParamsException
{
    private array $location;

    private function __construct(array $location, string $message)
    {
        $this->location = $location;
        parent::__construct($message);
    }

    public static function badArrayDataStorage(array $location): self
    {
        $message = sprintf(
            "Invalid location detected in ArrayDataStorage. This shouldn't happen and is likely a bug in the params library. Location was %s",
            implode(", ", $location)
        );

        return new self(
            $location,
            $message
        );
    }

    public static function badComplexDataStorage(array $location): self
    {
        $message = sprintf(
            "Invalid location detected in ComplexDataStorage. This shouldn't happen and is likely a bug in the params library. Location was %s",
            implode(", ", $location)
        );

        return new self(
            $location,
            $message
        );
    }

    public static function intNotAllowedComplexDataStorage(array $location): self
    {
        $message = sprintf(
            "Tried to use int as key to object in ComplexDataStorage. This shouldn't happen and is likely a bug in the params library. Location was %s",
            implode(", ", $location)
        );

        return new self(
            $location,
            $message
        );
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->location;
    }
}
