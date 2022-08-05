<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Traits\Constants;

/**
 * @method static SequenceResult NEXT()
 * @method static SequenceResult REUSE()
 * @method static SequenceResult FAIL()
 */
class SequenceResult
{
    use Constants;

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

    public static function fromBool(bool $result): SequenceResult {
        return $result ? self::NEXT() : self::FAIL();
    }
}
