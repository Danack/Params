<?php

declare(strict_types = 1);

namespace Params;

class ValidationProblem
{
    /**
     * The path of the parameter that was being validated.
     */
    private Path $path;

    private string $problemMessage;

    /**
     *
     * @param string $path
     * @param string $description
     */
    public function __construct(Path $path, string $description)
    {
        $this->path = $path;
        $this->problemMessage = $description;
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getProblemMessage(): string
    {
        return $this->problemMessage;
    }

    /**
     * TODO - this probably shouldn't be here?
     * Need to think about where message formatting should be done.
     */
    public function toString(): string
    {
        return $this->path->toString() . " " . $this->problemMessage;
    }
}
