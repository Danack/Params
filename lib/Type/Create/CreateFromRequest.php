<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;
use function Type\create;
use function Type\getPropertyDefinitionsForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return self
     * @throws \Type\Exception\ValidationException
     */
    public static function createFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $rules = getPropertyDefinitionsForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        $object = create(static::class, $rules, $dataStorage);
        /** @var $object self */
        return $object;
    }
}
