<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use function Type\createOrError;
use function Type\getPropertyDefinitionsForClass;

trait CreateOrErrorFromArray
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \Type\Exception\ValidationException
     */
    public static function createOrErrorFromArray(array $data)
    {
        $rules = getPropertyDefinitionsForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
