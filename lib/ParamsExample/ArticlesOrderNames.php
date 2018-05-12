<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\KnownOrderNames;

class ArticlesOrderNames implements KnownOrderNames
{
    public static $knownOrders = [
        'articleId',
        'date'
    ];

    /** @return string[] */
    public function getKnownOrderNames()
    {
        return [
            ArticleGetIndexParams::ARTICLE_ID_NAME,
            ArticleGetIndexParams::ARTICLE_DATE_NAME
        ];
    }
}
