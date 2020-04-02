<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\PatchOperation\PatchOperation;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\Exception\PatchFormatException;

use function Params\processOperations;
use function Params\createObjectFromParams;

/**
 * Class Params
 *
 * Validates multiple parameters at once, each according to their
 * own set of rules.
 *
 * Any validation problem will cause a ValidationException to be thrown.
 *
 */
class ParamsExecutor
{







}
