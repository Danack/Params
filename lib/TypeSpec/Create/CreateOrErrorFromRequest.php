<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use Psr\Http\Message\ServerRequestInterface;
use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\Exception;
use VarMap\Psr7VarMap;
use function TypeSpec\createOrError;
use function TypeSpec\getInputTypeSpecListForClass;

trait CreateOrErrorFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return array{0:object|null, 1:ValidationErrors|null}
     * @throws Exception\TypeSpecException
     * @throws Exception\ValidationException
     */
    public static function createOrErrorFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $rules = getInputTypeSpecListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
