<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use function TypeSpec\createOrError;
use function TypeSpec\getInputTypeSpecListForClass;

trait CreateOrErrorFromArray
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createOrErrorFromArray(array $data)
    {
        $rules = getInputTypeSpecListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
