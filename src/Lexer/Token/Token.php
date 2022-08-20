<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;
use AspectOverride\Lexer\Traits\ConstantContainer;
use JetBrains\PhpStorm\Language;

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
 * @method static Token COMMA()
 * @method static Token ANY()
 */
class Token implements TokenMatches
{
    use ConstantContainer;

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
    public const COMMA = ';';
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

    public function matches(int $key, string $token, string $normalizedToken): Sequence {
        switch ($normalizedToken) {
            case self::ANY:  return Sequence::NEXT();
            default: return $this->token === $normalizedToken ? Sequence::NEXT() : Sequence::FAIL();
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

    public static function anyUntilEmptyStack(TokenMatches $add, TokenMatches $remove): AnyUntilEmptyStack {
        return new AnyUntilEmptyStack($add, $remove);
    }

    public static function not(TokenMatches $token): Not {
        return new Not($token);
    }

    public static function _and(TokenMatches $first, TokenMatches $second): AndToken {
        return new AndToken($first, $second);
    }

    public static function regex(string $regex): Regex {
        return new Regex($regex);
    }
}