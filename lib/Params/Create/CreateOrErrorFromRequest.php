<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
use Params\Exception;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;
use function Params\createOrError;

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

        $rules = static::getInputParameterList();

        // TODO - fix
        $dataLocator = DataStorage::fromArray($data);

        return createOrError(static::class, $namedRules, $variableMap);
    }
}
