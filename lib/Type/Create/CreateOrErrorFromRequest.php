<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use Type\Exception;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;
use function Type\createOrError;
use function Type\getPropertyDefinitionsForClass;

trait CreateOrErrorFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return array{0:object|null, 1:ValidationErrors|null}
     * @throws Exception\ParamsException
     * @throws Exception\ValidationException
     */
    public static function createOrErrorFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $rules = getPropertyDefinitionsForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
