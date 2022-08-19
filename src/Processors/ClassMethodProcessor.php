<?php

namespace AspectOverride\Processors;

use AspectOverride\Lexer\OnSequenceMatched;
use AspectOverride\Lexer\SequenceGenerator;
use AspectOverride\Lexer\SequenceRules;
use AspectOverride\Lexer\Token\Capture;
use AspectOverride\Lexer\Token\Token as T;
use AspectOverride\Utility\TokenUtility;

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
                T::OPENING_PAREN(),
                T::capture(
                    T::anyUntilEmptyStack(
                        T::OPENING_BRACKET(),
                        T::CLOSING_BRACKET()
                    )
                )
            ], new class implements OnSequenceMatched {
                function __invoke(array $captures): array {
                    $isVoid = array_reduce($captures, function (bool $carry, Capture $capture) {
                        return $carry && in_array($capture->text, ['void',':void',':void{']);
                    }, true);
                    TokenUtility::wrapCaptures(
                        ($isVoid ? '' : 'return ') . "\AspectOverride\Facades\Instance::wrapAround(__CLASS__, __FUNCTION__, func_get_args(), ",
                        ");",
                        $captures
                    );
                    return $captures;
                }
            })
        ]);
        return $lexer->transform($data);
    }

    public function onNewFile(): void {}
}
