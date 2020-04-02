<?php

declare(strict_types=1);

namespace Params\Create;

use Params\Exception\ValidationException;
use Params\Path;
use VarMap\VarMap;
use function Params\createArrayForTypeWithRules;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self[]
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createArrayOfTypeFromArray(array $data)
    {
        // @TODO - check interface is implemented here.

        if (method_exists(self::class, 'getInputParameterList') === true) {
            $rules = static::getInputParameterList();
        }
        else {
            throw new \Exception("Borken.");
        }

        // TODO - this should be a root path?
        $path = Path::initial();
        $validationResult = createArrayForTypeWithRules($path, self::class, $data, $rules);

        if ($validationResult->anyErrorsFound() === true) {
            throw new ValidationException(
                "Validation problems",
                $validationResult->getValidationProblems()
            );
        }

        $objects = $validationResult->getValue();

        /** @var self[] self */
        return $objects;
    }
}
