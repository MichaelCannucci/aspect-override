<?php

namespace AspectOverride\Token\Machine\Traits;

trait ContainsTokenStateMachine {

    /**
     * @var int
     */
    protected $lastState = 0;

    /**
     * @var int
     */
    protected $nextState = 0;

    /**
     * @var null|\PhpToken
     */
    protected $lastToken = null;

    public function stateBroken(): void {
        $this->nextState = 0;
    }

    public function tickState(array $events, \PhpToken $token): string {
        $text = $token->text;
        if ($this->nextState !== $this->lastState) {
            if (array_key_exists($this->nextState, $events)) {
                $text = $this->events[$this->nextState]($token, $this);
            }
        }
        $this->lastState = $this->nextState;
        $this->lastToken = $token;
        return $text;
    }
}