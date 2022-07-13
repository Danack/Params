<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\Create\CreateArrayOfTypeFromArray;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\SafeAccess;
use TypeSpec\TypeSpec;

class ReviewScore implements TypeSpec
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
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'score',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new InputTypeSpec(
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
