<?php

namespace AspectOverride\Token\Machine;

use AspectOverride\Token\Machine\Traits\ContainsTokenStateMachine;

class ClassTokenMachine implements TokenMachineInterface {

    use ContainsTokenStateMachine;

    public const UNKNOWN = 0;
    public const FUNCTION_KEYWORD = 1;
    public const FUNCTION_NAME = 2;
    public const ARGUMENT_START = 3;
    public const ARGUMENT_PARAMETERS = 4;
    public const ARGUMENT_END = 5;
    public const FUNCTION_RETURN_TYPE = 6;
    public const FUNCTION_START = 7;
    public const FUNCTION_END = 8;

    /**
     * @var string
     */
    protected $rawArguments;

    /**
     * @var bool
     */
    protected $voidReturn = false;

    /**
     * @var int
     */
    protected $stack = 0;

    /**
     * @var callable[]
     */
    private $events;

    /**
     * @param callable[] $events
     */
    public function __construct(array $events = []) {
        $this->events = $events;
    }

    public function voidReturn(): bool {
        return $this->voidReturn;
    }

    public function rawArguments(): string {
        return $this->rawArguments;
    }

    public function process(\PhpToken $token, ?\PhpToken $before = null): string {
        if ($token->isIgnorable()) {
            return $token->text;
        }

        switch($this->lastState) {
            case self::UNKNOWN:
                if ($token->is(T_FUNCTION)) {
                    $this->nextState = self::FUNCTION_KEYWORD;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::FUNCTION_START:
                if ($token->getTokenName() === '{') {
                    $this->stack += 1;
                } elseif ($token->getTokenName() === '}') {
                    $this->stack -= 1;
                }
                if ($this->stack === 0) {
                    $this->nextState = self::FUNCTION_END;
                }
            break;
            case self::FUNCTION_NAME:
                if ($token->is("(")) {
                    $this->nextState = self::ARGUMENT_START;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::ARGUMENT_START:
            case self::ARGUMENT_PARAMETERS:
                if ($token->is([T_STRING, T_VARIABLE, ',', '&'])) {
                    $this->rawArguments .= $token->text;
                    $this->nextState = self::ARGUMENT_PARAMETERS;
                } elseif ($token->is(")")) {
                    $this->nextState = self::ARGUMENT_END;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::ARGUMENT_END:
                if ($token->is(':')) {
                    $this->nextState = self::FUNCTION_RETURN_TYPE;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::FUNCTION_RETURN_TYPE:
                if ($token->is(T_STRING)) {
                    $this->voidReturn = strtolower(trim($token->text)) === 'void';
                    $this->nextState = self::FUNCTION_START;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::FUNCTION_KEYWORD:
                $this->nextState = self::FUNCTION_NAME;
            break;
            default:
                $this->stateBroken();
            break;
        }


        return $this->tickState($this->events, $token);
    }

    public function reset(): void
    {
        $this->rawArguments = '';
        $this->voidReturn = false;
        $this->stack = 0;
    }
}
