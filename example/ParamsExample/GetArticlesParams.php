<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\Create\CreateFromRequest;
use Params\Create\CreateFromVarMap;
use Params\Create\CreateOrErrorFromVarMap;
use Params\ExtractRule\GetIntOrDefault;
use Params\ExtractRule\GetStringOrDefault;
use Params\Param;
use Params\SafeAccess;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MaxLength;
use Params\ProcessRule\MinIntValue;
use Params\ProcessRule\Order;
use Params\ProcessRule\SkipIfNull;
use Params\Value\Ordering;
use Params\InputParameterList;

class GetArticlesParams implements InputParameterList
{
    use SafeAccess;
    use CreateFromRequest;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    const LIMIT_DEFAULT = 10;

    const LIMIT_MIN = 1;
    const LIMIT_MAX = 200;

    const ARTICLE_ID_NAME = 'articleId';
    const ARTICLE_ID_INTERNAL = 'articleId';

    const ARTICLE_DATE_NAME = 'date';
    const ARTICLE_DATE_INTERNAL = 'date';

    const OFFSET_MAX = 1000000000000000;

    /** @return string[] */
    public static function getKnownOrderNames()
    {
        return [
            GetArticlesParams::ARTICLE_ID_NAME,
            GetArticlesParams::ARTICLE_DATE_NAME
        ];
    }

    /** @var Ordering  */
    private $ordering;

    /** @var int  */
    private $limit;

    /** @var int|null  */
    private $afterId;

    public function __construct(Ordering $ordering, int $limit, ?int $afterId)
    {
        $this->ordering = $ordering;
        $this->limit = $limit;
        $this->afterId = $afterId;
    }

    /**
     * @return \Params\Param[]
     */
    public static function getInputParameterList()
    {
        return [
            new Param(
                'order',
                new GetStringOrDefault('-date'),
                new Order(self::getKnownOrderNames())
            ),
            new Param(
                'limit',
                new GetIntOrDefault(self::LIMIT_DEFAULT),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX)
            ),
            new Param(
                'after',
                new GetStringOrDefault(null),
                new SkipIfNull(),
                new MinIntValue(0),
                new MaxIntValue(self::OFFSET_MAX)
            )
        ];
    }

    /**
     * @return Ordering
     */
    public function getOrdering(): Ordering
    {
        return $this->ordering;
    }

    /**
     * @return int|null
     */
    public function getAfterId(): ?int
    {
        return $this->afterId;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
