<?php

namespace AspectOverride\Token\Machine\Traits;

trait ContainsTokenStateMachine {

    /**
     * @var int
     */
    protected $lastState = 0;

    protected $nextState = 0;

    public function inStates(int ...$states): bool {
        return in_array($this->lastState, $states);
    }

    public function stateBroken(): void {
        $this->nextState = 0;
    }

    public function tickState(array $events, \PhpToken $token): string {
        if ($this->nextState !== $this->lastState) {
            if (array_key_exists($this->nextState, $events)) {
                $this->lastState = $this->nextState;
                return $this->events[$this->nextState]($token, $this);
            }
        }
        if ($this->nextState === 0) { //Rest
            $this->lastState = 0;
        }
        return $token->text;
    }
}