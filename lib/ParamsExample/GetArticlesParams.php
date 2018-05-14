<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\ParamsValidator;
use Params\Rule\CheckSetOrDefault;
use Params\Rule\MaxIntValue;
use Params\Rule\MinIntValue;
use Params\Rule\MaxLength;
use Params\Rule\SkipIfNull;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\Rule\Order;
use Params\Rule\IntegerInput;
use Params\Value\Ordering;
use Params\Params;

class GetArticlesParams
{
    use SafeAccess;

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
     * @param VarMap $variableMap
     * @return GetArticlesParams
     * @throws \Params\Exception\ValidationException
     * @throws \Params\Exception\ParamsException
     */
    public static function fromVarMap(VarMap $variableMap) : GetArticlesParams
    {
        $params = [
            'order' => [
                new CheckSetOrDefault('-date', $variableMap),
                new MaxLength(1024),
                new Order(self::getKnownOrderNames()),
            ],
            'limit' => [
                new CheckSetOrDefault((string)self::LIMIT_DEFAULT, $variableMap),
                new IntegerInput(),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX),
            ],
            'after' => [
                new CheckSetOrDefault(null, $variableMap),
                new SkipIfNull(),
                new MinIntValue(0),
                new MaxIntValue(self::OFFSET_MAX),
            ],
        ];

        list($order, $limit, $offset) = Params::validate($params);

        return new GetArticlesParams($order, $limit, $offset);
    }


    /**
     * @param VarMap $variableMap
     * Actually returns [ArticleGetIndexParams, array]
     * @return mixed
     */
    public static function fromVarMapWithErrorReturned(VarMap $variableMap)
    {
        $validator = new ParamsValidator();

        $order = $validator->validate('order', [
            new CheckSetOrDefault('-date', $variableMap),
            new MaxLength(1024),
            new Order(self::getKnownOrderNames()),
        ]);

        $limit = $validator->validate('limit', [
            new CheckSetOrDefault((string)self::LIMIT_DEFAULT, $variableMap),
            new IntegerInput(),
            new MinIntValue(self::LIMIT_MIN),
            new MaxIntValue(self::LIMIT_MAX),
        ]);

        $offset = $validator->validate('offset', [
            new CheckSetOrDefault(null, $variableMap),
            new SkipIfNull(),
            new MinIntValue(0),
            new MaxIntValue(self::OFFSET_MAX),
        ]);

        $errors = $validator->getValidationProblems();

        if (count($errors) !== 0) {
            return [null, $errors];
        }

        return [new GetArticlesParams($order, $limit, $offset), null];
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
