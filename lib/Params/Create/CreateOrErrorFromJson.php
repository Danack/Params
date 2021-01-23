<?php

declare(strict_types=1);

namespace Params\Create;

use Params\InputStorage\ArrayInputStorage;
use function JsonSafe\json_decode_safe;
use function Params\createOrError;

trait CreateOrErrorFromJson
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromJson($json)
    {
        $data = json_decode_safe($json);

        $namedRules = static::getInputParameterList();
        $dataLocator = ArrayInputStorage::fromArray($data);

        return createOrError(static::class, $namedRules, $dataLocator);
    }
}
