<?php

declare(strict_types=1);

namespace TypeExample;

use Type\Create\CreateFromRequest;
use Type\Create\CreateFromVarMap;
use Type\Create\CreateOrErrorFromVarMap;
use Type\ExtractRule\GetIntOrDefault;
use Type\ExtractRule\GetStringOrDefault;
use Type\PropertyDefinition;
use Type\SafeAccess;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MaxLength;
use Type\ProcessRule\MinIntValue;
use Type\ProcessRule\Order;
use Type\ProcessRule\SkipIfNull;
use Type\Value\Ordering;
use Type\Type;

class GetArticlesParams implements Type
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
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'ordering',
                new GetStringOrDefault('-date'),
                new Order(self::getKnownOrderNames())
            ),
            new PropertyDefinition(
                'limit',
                new GetIntOrDefault(self::LIMIT_DEFAULT),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX)
            ),
            new PropertyDefinition(
                'afterId',
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
