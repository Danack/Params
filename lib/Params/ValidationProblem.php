<?php

declare(strict_types = 1);

namespace Params;

class ValidationProblem
{

    /**
     * The identifier
     *
     * @var string
     */
    private string $identifier;

    private string $problemMessage;

    /**
     *
     * @param string $path
     * @param string $description
     */
    public function __construct(string $path, string $description)
    {
        $this->identifier = $path;
        $this->problemMessage = $description;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getProblemMessage(): string
    {
        return $this->problemMessage;
    }

    public function toString(): string
    {
        return $this->identifier . " " . $this->problemMessage;
    }
}
