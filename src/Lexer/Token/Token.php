<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;
use AspectOverride\Lexer\Traits\Constants;

/**
 * @method static Token PUBLIC()
 * @method static Token PROTECTED()
 * @method static Token PRIVATE()
 * @method static Token FUNCTION()
 * @method static Token OPENING_PAREN()
 * @method static Token CLOSING_PAREN()
 * @method static Token OPENING_BRACKET()
 * @method static Token CLOSING_BRACKET()
 * @method static Token RETURN()
 * @method static Token COLON()
 * @method static Token ANY()
 */
class Token implements TokenMatches
{
    use Constants;

    public const PUBLIC = 'public';
    public const PROTECTED = 'protected';
    public const PRIVATE = 'private';
    public const FUNCTION = 'function';
    public const OPENING_PAREN = '(';
    public const CLOSING_PAREN = ')';
    public const OPENING_BRACKET = '{';
    public const CLOSING_BRACKET = '}';
    public const RETURN = 'return';
    public const COLON = ':';
    public const ANY = '*';

    /**
     * @readonly
     * @var string
     */
    public $token;

    public function __construct(string $token) {
        $this->token = $token;
    }

    public static function of(string $token): self {
        return new self($token);
    }

    public function matches(string $token): SequenceResult {
        switch ($token) {
            case self::ANY:  return SequenceResult::NEXT();
            default: return SequenceResult::fromBool($this->token === $token);
        }
    }

    public static function anyOf(Token ...$tokens): AnyOf {
        return new AnyOf($tokens);
    }

    public static function anyUntil(TokenMatches $token): AnyUntil {
        return new AnyUntil($token);
    }

    public static function capture(TokenMatches $token): CaptureWith {
        return new CaptureWith($token);
    }
}