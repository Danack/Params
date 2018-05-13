<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\KnownOrderNames;

class ArticlesOrderNames implements KnownOrderNames
{
    /** @return string[] */
    public function getKnownOrderNames()
    {
        return [
            'articleId',
            'date'
        ];
    }
}
