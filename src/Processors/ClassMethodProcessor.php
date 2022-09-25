<?php

namespace AspectOverride\Processors;

use AspectOverride\Token\TokenStream;
use AspectOverride\Token\TokenMachine;

class ClassMethodProcessor implements CodeProcessorInterface {

    /**
     * @var TokenStream
     */
    protected $tokenizer;

    public function __construct()
    {
        $this->tokenizer = new TokenStream(new TokenMachine([
            TokenMachine::FUNCTION_START => function (\PhpToken $token, TokenMachine $machine) {
                if($machine->capturedArguments) {
                    $argNames = explode(',', str_replace(['$', '&'], '',$machine->capturedArguments));
                    $quotedNames = "'" . implode("','", $argNames) . "'";
                    $gatherArgs = "compact($quotedNames)";
                } else {
                    $gatherArgs = "[]";
                }
                return $token->text .
                    /** @lang PHP */
                    "list(\$args, \$result) = \AspectOverride\Facades\AspectOverride::wrapAround(" .
                    "__CLASS__, __FUNCTION__, $gatherArgs, function($machine->capturedArguments){";
            },
            TokenMachine::FUNCTION_END => function (\PhpToken $token, TokenMachine $machine) {
                $return = $machine->voidReturn ? '' : /** @lang PHP */ 'return $result;';
                $overwriteArguments = str_contains($machine->capturedArguments, '&') ?
                    /** @lang PHP */ 'if($args) { extract($args, EXTR_OVERWRITE); }' : '';
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

    public function onNewFile(): void {
        $this->tokenizer->getMachine()->reset();
    }
}
