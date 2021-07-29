<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataStorage\ArrayDataStorage;
use function Params\createOrError;

trait CreateOrErrorFromArray
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromArray(array $data)
    {
        $namedRules = static::getInputParameterList();
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $namedRules, $dataStorage);
    }
}
