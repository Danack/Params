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
use Params\MagicValidator;

class ArticleGetIndexParams
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
            ArticleGetIndexParams::ARTICLE_ID_NAME,
            ArticleGetIndexParams::ARTICLE_DATE_NAME
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
     * @return ArticleGetIndexParams
     * @throws \Params\Exception\ValidationException
     * @throws \Params\Exception\ParamsException
     */
    public static function fromVarMap(VarMap $variableMap) : ArticleGetIndexParams
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

        return new ArticleGetIndexParams($order, $limit, $offset);
    }

    /**
     * @param VarMap $variableMap
     * @return [ArticleGetIndexParams, array $errors]
     */
    public static function fromMagic(VarMap $variableMap)
    {
        $validator = new MagicValidator();

        $order = &$validator->addRule('order', [
            new CheckSetOrDefault('-date', $variableMap),
            new MaxLength(1024),
            new Order(self::getKnownOrderNames()),
        ]);

        $limit = &$validator->addRule('limit', [
            new CheckSetOrDefault((string)self::LIMIT_DEFAULT, $variableMap),
            new IntegerInput(),
            new MinIntValue(self::LIMIT_MIN),
            new MaxIntValue(self::LIMIT_MAX),
        ]);

        $offset = &$validator->addRule('offset', [
            new CheckSetOrDefault(null, $variableMap),
            new SkipIfNull(),
            new MinIntValue(0),
            new MaxIntValue(self::OFFSET_MAX),
        ]);

        $errors = $validator->validate();

        if (count($errors) !== 0) {
            return [null, $errors];
        }

        return [new ArticleGetIndexParams($order, $limit, $offset), null];
    }


    /**
     * @param VarMap $variableMap
     * @return [ArticleGetIndexParams, array $errors]
     */
    public static function fromLessMagic(VarMap $variableMap)
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

        return [new ArticleGetIndexParams($order, $limit, $offset), null];
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
