<?php

namespace AspectOverride\Lexer;

class TokenMachine
{
    public const START                = 1;
    public const FUNCTION_KEYWORD     = 2;
    public const FUNCTION_NAME        = 3;
    public const PARAMETER_START      = 4;
    public const PARAMETER_ARGUMENTS  = 5;
    public const PARAMETER_END        = 6;
    public const FUNCTION_RETURN_TYPE = 7;
    public const FUNCTION_START       = 8;
    public const FUNCTION_END         = 9;

    public $voidReturn = false;
    public $capturedArguments = "";

    private $state = self::START;
    private $stack = 0;

    /**
     * @var callable[]
     */
    private $events;

    public function __construct(array $events = [])
    {
        $this->events = $events;
    }

    public function process(\PhpToken $token): string
    {
        if ($token->isIgnorable()) {
            return $token->text;
        }
        // Basically ignore everything until we get to the last closing brace of the function
        // Disregarding states at this point
        if ($this->inState(self::FUNCTION_START)) {
            if ($token->getTokenName() === '{') {
                $this->stack += 1;
            } else if ($token->getTokenName() === '}') {
                $this->stack -= 1;
            }
            if ($this->stack === 0) {
                $return = $this->checkStateAndReturnToken($this->state, self::FUNCTION_END, $token);
                $this->reset();
                return $return;
            }
            return $token->text;
        }

        if ($token->is(T_FUNCTION) && $this->inState(self::START, self::START)):
            $nextState = self::FUNCTION_KEYWORD;
        elseif ($this->inState(self::FUNCTION_KEYWORD)):
            $nextState = self::FUNCTION_NAME;
        elseif ($token->getTokenName() === "(" && $this->inState(self::FUNCTION_NAME)):
            $nextState = self::PARAMETER_START;
        elseif (($token->is([T_STRING, T_VARIABLE]) || $token->getTokenName() === ',') && $this->inState(self::PARAMETER_START, self::PARAMETER_ARGUMENTS)):
            $this->capturedArguments .= $token->text;
            $nextState = self::PARAMETER_ARGUMENTS;
        elseif ($token->getTokenName() === ")" && $this->inState(self::PARAMETER_ARGUMENTS, self::PARAMETER_START)):
            $nextState = self::PARAMETER_END;
        elseif (($token->is(T_STRING) || $token->getTokenName() === ':') && $this->inState(self::PARAMETER_END, self::FUNCTION_RETURN_TYPE)):
            if($token->is(T_STRING)) {
                $this->voidReturn = strtolower(trim($token->text)) === 'void';
            }
            $nextState = self::FUNCTION_RETURN_TYPE;
        elseif ($token->getTokenName() === '{' && $this->inState(self::PARAMETER_END, self::FUNCTION_RETURN_TYPE)):
            $nextState = self::FUNCTION_START;
            $this->stack = 1;
        else:
            $nextState = self::START;
        endif;
        return $this->checkStateAndReturnToken($this->state, $nextState, $token);
    }

    public function reset()
    {
        $this->state = self::START;
        $this->stack = 0;
        $this->voidReturn = false;
        $this->capturedArguments = "";
    }

    protected function checkStateAndReturnToken(int $previousState, int $nextState, \PhpToken $token): string
    {
        if($previousState !== $nextState) {
            $this->state = $nextState;
            if (array_key_exists($nextState, $this->events)) {
                return $this->events[$nextState]($token, $this);
            }
        }
        return $token->text;
    }

    private function inState(int ...$states): bool
    {
        return in_array($this->state, $states);
    }
}