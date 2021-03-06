<?php

declare(strict_types=1);

namespace Params\Create;

use Params\InputStorage\ArrayInputStorage;
use Psr\Http\Message\ServerRequestInterface;
use function Params\create;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return self
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromRequest(ServerRequestInterface $request)
    {
        $rules = static::getInputParameterList();

        $dataLocator = ArrayInputStorage::fromArray($request->getQueryParams());

        $object = create(static::class, $rules, $dataLocator);
        /** @var $object self */
        return $object;
    }
}
