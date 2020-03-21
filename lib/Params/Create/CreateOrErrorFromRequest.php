<?php

declare(strict_types=1);

namespace Params\Create;

use Params\Exception;
use Params\Params;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;

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

        $namedRules = static::getInputToParamInfoList();

        return Params::createOrError(static::class, $namedRules, $variableMap);
    }
}
