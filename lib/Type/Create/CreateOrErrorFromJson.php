<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use function JsonSafe\json_decode_safe;
use function Type\createOrError;
use function Type\getPropertyDefinitionsForClass;

trait CreateOrErrorFromJson
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \Type\Exception\ValidationException
     */
    public static function createOrErrorFromJson($json)
    {
        $data = json_decode_safe($json);

        $rules = getPropertyDefinitionsForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
