<?php

namespace AspectOverride\Processors;

use AspectOverride\Token\Tokenizer;
use AspectOverride\Token\TokenMachine;

class ClassMethodProcessor extends AbstractProcessor {
    public const NAME = 'aspect_mock_method_override';

    /**
     *
     * Add the injection points for the monkey-patching
     *
     * @param string $data
     * @return string
     */
    public function transform(string $data): string {
        return $this->getTokenizer()->transform($data);
    }

    protected function getTokenizer(): Tokenizer {
        // stream user filter related thing, since the constructor isn't called we can't construct things normally
        // hence the static variable
        static $tokenizer;
        if (!$tokenizer) {
            $tokenizer = new Tokenizer(new TokenMachine([
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
                        "list(\$args, \$result) = \AspectOverride\Facades\Instance::wrapAround(" .
                        "__CLASS__, __FUNCTION__, $gatherArgs, function($machine->capturedArguments){";
                },
                TokenMachine::FUNCTION_END => function (\PhpToken $token, TokenMachine $machine) {
                    $return = $machine->voidReturn ? '' : /** @lang PHP */ 'return $result;';
                    return $token->text . /** @lang PHP */"); if(\$args) { extract(\$args, EXTR_OVERWRITE); } $return }";
                }
            ]));
        }
        return $tokenizer;
    }

    public function onNewFile(): void {
        $this->getTokenizer()->getMachine()->reset();
    }
}
