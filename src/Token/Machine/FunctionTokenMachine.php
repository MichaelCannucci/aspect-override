<?php

namespace AspectOverride\Token\Machine;

use AspectOverride\Token\Machine\Traits\ContainsTokenStateMachine;

/**
 * @wip
 */
class FunctionTokenMachine implements TokenMachineInterface {
    public const START = 1;
    public const QUALIFIED_NAMESPACE_START = 2;
    public const FULLY_QUALIFIED_NAMESPACE = 3;
    public const RELATIVE_QUALIFIED_NAMESPACE = 4;
    public const FUNCTION_START = 5;
    public const ARGUMENT_PARAMETERS = 6;
    public const ARGUMENT_END = 7;

    /** Function that shouldn't be patched because it breaks things or doesn't make sense */
    private const DENY_LIST = [
        'extract'      => true, // extract does nothing because it runs in another scope, so no variables change
        'if'           => true, // Language Keyword
        'elseif'       => true, // Language Keyword
        'else'         => true, // Language Keyword
        'function'     => true, // Language Keyword
        'while'        => true, // Language Keyword
        'unset'        => true, // Language Keyword
        'isset'        => true, // Language Keyword
        'empty'        => true, // Language Keyword
        'die'          => true, // Language Keyword
        'use'          => true, // Language Keyword
        'match'        => true, // Language Keyword
        'declare'      => true, // Language Keyword
        'list'         => true, // Language Keyword
        'array'        => true, // Language Keyword
        'require'      => true, // Language Keyword
        'require_once' => true, // Language Keyword
        'include'      => true, // Language Keyword
        'include_once' => true, // Language Keyword
        'echo'         => true, // Language Keyword
    ];


    use ContainsTokenStateMachine;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $rawArguments;

    /**
     * @var string[]
     */
    protected $namespaces;

    /**
     * @var string[]
     */
    protected $functions;

    /**
     * @param callable[] $events
     */
    protected $events;

    public function __construct(array $events = []) {
        $this->events = $events;
    }

    public function process(\PhpToken $token): string {
        if ($token->isIgnorable()) {
            return $token->text;
        }

        switch ($this->lastState) {
            case self::START:
                if ($this->isFunction($token)) {
                    $this->name = $token->text;
                    $this->nextState = self::FUNCTION_START;
                }
                if ($token->is(T_USE)) {
                    $this->nextState = self::QUALIFIED_NAMESPACE_START;
                }
                if ($token->is(T_NAME_FULLY_QUALIFIED)) {
                    $this->nextState = self::FULLY_QUALIFIED_NAMESPACE;
                }
                if ($token->is(T_NAME_RELATIVE)) {
                    $this->nextState = self::RELATIVE_QUALIFIED_NAMESPACE;
                }
            break;
//            case self::FULLY_QUALIFIED_NAMESPACE:
//                // TODO
//            break;
//            case self::RELATIVE_QUALIFIED_NAMESPACE:
//                // TODO
//            break;
            case self::QUALIFIED_NAMESPACE_START:
                if ($token->is(T_NAME_QUALIFIED)) {
                    $this->namespaces[] = $token->text;
                    $this->nextState = self::START;
                } else {
                    $this->stateBroken();
                }
            break;
            case self::FUNCTION_START:
                if ($token->is('(')) {
                    $this->nextState = self::ARGUMENT_PARAMETERS;
                } else {
                    $this->stateBroken();
                }
            break;
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
                if ($token->is(';')) {
                    $this->nextState = self::START;
                    $this->functions[] = $this->name;
                    $this->name = '';
                } elseif ($this->isFunction($token)) {
                    $this->nextState = self::FUNCTION_START;
                } else {
                    $this->stateBroken();
                }
            break;
            default:
                $this->stateBroken();
            break;

        }

        return $this->tickState($this->events, $token);
    }

    protected function isFunction(\PhpToken $token): bool {
        return $token->is(T_STRING) &&
        !$this->lastToken->is([T_FUNCTION, T_NEW]) &&
        $this->allowedFunction($token->text) &&
        $this->functionExists($token->text);
    }

    protected function functionExists(string $function): bool {
        if (function_exists($function)) {
            return true;
        }
        foreach ($this->namespaces as $namespace) {
            if (function_exists($namespace . '\\' . $function)) {
                return true;
            }
        }
        return false;
    }

    protected function allowedFunction(string $name): bool{
        return !isset(self::DENY_LIST[$name]);
    }

    public function reset(): void
    {
        $this->lastState = 0;
        $this->nextState = 0;
        $this->name = '';
        $this->rawArguments = '';
        $this->namespaces = [];
        $this->functions = [];
    }
}