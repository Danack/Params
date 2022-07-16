<?php

declare(strict_types=1);

namespace TypeSpecExample;

use TypeSpec\Create\CreateFromRequest;
use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\Create\CreateOrErrorFromVarMap;
use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\InputTypeSpec;
use TypeSpec\SafeAccess;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\ProcessRule\Order;
use TypeSpec\ProcessRule\SkipIfNull;
use TypeSpec\Value\Ordering;
use TypeSpec\TypeSpec;

// TODO - change to type?
class GetArticlesParams implements TypeSpec
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
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'ordering',
                new GetStringOrDefault('-date'),
                new Order(self::getKnownOrderNames())
            ),
            new InputTypeSpec(
                'limit',
                new GetIntOrDefault(self::LIMIT_DEFAULT),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX)
            ),
            new InputTypeSpec(
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
