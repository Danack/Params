<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\Create\CreateFromVarMap;
use Type\Create\CreateArrayOfTypeFromArray;
use Type\ExtractRule\GetInt;
use Type\ExtractRule\GetString;
use Type\PropertyDefinition;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinLength;
use Type\SafeAccess;
use Type\Type;

class ReviewScore implements Type
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;

    private int $score;

    private string $comment;

    /**
     *
     * @param int $foo
     * @param string $bar
     */
    public function __construct(int $score, string $comment)
    {
        $this->score = $score;
        $this->comment = $comment;
    }

    /**
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'score',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new PropertyDefinition(
                'comment',
                new GetString(),
                new MinLength(4)
            ),
        ];
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
