<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Create\CreateOrErrorFromInput;

class BarParamsCreateOrErrorFromInput extends BarParams
{
    use CreateOrErrorFromInput;
}
