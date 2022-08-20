<?php

namespace AspectOverride\Processors;

use AspectOverride\Lexer\OnSequenceMatched;
use AspectOverride\Lexer\SequenceGenerator;
use AspectOverride\Lexer\SequenceRules;
use AspectOverride\Lexer\Token\Capture;
use AspectOverride\Lexer\Token\Token as T;

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
        $lexer = New SequenceRules([
            new SequenceGenerator([
                T::anyOf(
                    T::PRIVATE(), T::PROTECTED(), T::PUBLIC()
                ),
                T::FUNCTION(),
                T::capture(
                    T::anyUntilEmptyStack(
                        T::OPENING_BRACKET(),
                        T::CLOSING_BRACKET()
                    )
                )
            ], new class implements OnSequenceMatched {
                /** @param Capture[] $captures */
                function __invoke(array $captures): array {
                    $void = false;
                    $fullText = implode('', array_map(function(Capture $capture) {return $capture->text;}, $captures));
                    $arguments = "";
                    if(preg_match("/\((.*?)\)/", $fullText, $matches)) {
                        $arguments = $matches[1];
                    }
                    foreach ($captures as $capture) {
                        if(!$void) {
                            $void = in_array($capture->text, ['void',':void',':void{']);
                        }
                        if($capture->text === '{') {
                            $capture->text .= ($void ? '' : 'return ') .
                                "\AspectOverride\Facades\Instance::wrapAround(" .
                                "__CLASS__, __FUNCTION__, func_get_args(), function($arguments){";
                            break;
                        }
                    }
                    $last = $captures[count($captures) - 1];
                    $last->text .= ');}';
                    return $captures;
                }
            })
        ]);
        return $lexer->transform($data);
    }

    public function onNewFile(): void {}
}
