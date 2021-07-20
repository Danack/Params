<?php

declare(strict_types = 1);

namespace Params\InputStorage;

/**
 * When iterating over the input data, we need to be able to
 * store the current 'path' so that error messages can point
 * to the place where the error lies. e.g. for an input array:
 *
 * $foo = ['bar' => [0, 1, 50000, 2]];
 *
 * If all the 'bar' values are supposed to be between 0 and 10,
 * then there would be an error at /bar/2
 *
 * The path format is JSON pointer: https://tools.ietf.org/html/rfc6901
 *
 */
interface InputStorage
{
    /**
     * Get the value from the current position in the storage.
     * @return mixed
     * @TODO - could this be converted so that we are only ever
     * dealing with strings?
     */
    public function getCurrentValue();

    /**
     * Get the current path in JSON pointer format. e.g. /bar/2
     *
     * @TODO this seems a terrible misnaming. Is it path or pointer?
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Clone the InputStorage and move the position of the path in the
     * newly created instance, and return that new instance.
     *
     * This allows iterating over and into structures with the current
     * position kept in the same position for existing references to the
     * InputStorage.
     *
     * @param int|string $name
     * @return self
     */
    public function moveKey($name): self;

    /**
     * Is there a value available available in the current path position.
     *
     * @return bool
     */
    public function isValueAvailable(): bool;
}
