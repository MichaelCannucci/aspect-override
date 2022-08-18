<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Traits\ConstantContainer;

/**
 * @method static Sequence NEXT()
 * @method static Sequence REUSE()
 * @method static Sequence FAIL()
 */
class Sequence
{
    use ConstantContainer;

    public const NEXT = 1;
    public const REUSE = 2;
    public const FAIL = 3;

    /**
     * @var int
     */
    public $value;

    public function __construct(int $result)
    {
        $this->value = $result;
    }

    public static function fromBool(bool $result): Sequence {
        return $result ? self::NEXT() : self::FAIL();
    }
}
