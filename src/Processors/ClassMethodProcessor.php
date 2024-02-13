<?php

namespace AspectOverride\Processors;

use AspectOverride\Token\TokenStream;
use AspectOverride\Token\Machine\ClassTokenMachine;

class ClassMethodProcessor implements CodeProcessorInterface {
    /**
     * @var TokenStream
     */
    protected $tokenizer;

    public function __construct() {
        $this->tokenizer = new TokenStream(new ClassTokenMachine([
            ClassTokenMachine::FUNCTION_START => function (\PhpToken $token, ClassTokenMachine $machine) {
                if ($machine->rawArguments()) {
                    $argNames = explode(',', str_replace(['$', '&'], '', $machine->rawArguments()));
                    $quotedNames = "'" . implode("','", $argNames) . "'";
                    $gatherArgs = "compact($quotedNames)";
                } else {
                    $gatherArgs = "[]";
                }
                $rawArgs = $machine->rawArguments();
                return $token->text .
                    /** @lang PHP */
                    "list(\$args, \$result) = \AspectOverride\Facades\AspectOverride::wrapAround(" .
                    "__CLASS__, __FUNCTION__, $gatherArgs, function($rawArgs){";
            },
            ClassTokenMachine::FUNCTION_END => function (\PhpToken $token, ClassTokenMachine $machine) {
                $return = $machine->voidReturn() ? '' : /** @lang PHP */ 'return $result;';
                $overwriteArguments = str_contains($machine->rawArguments(), '&')
                    ? /** @lang PHP */ 'if($args) { extract($args, EXTR_OVERWRITE); }'
                    : '';

                return $token->text . /** @lang PHP */ "); $overwriteArguments $return }";
            }
        ]));
    }

    /**
     *
     * Add the injection points for the monkey-patching
     *
     * @param string $data
     * @return string
     */
    public function transform(string $data): string {
        return $this->tokenizer->transform($data);
    }

    public function onNew(): void {
        $this->tokenizer->reset();
    }
}
