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

    private const BEFORE_PATTERN = '/(private|protected|public)?(\s+function\s+\S*)\(([\s\S]*?)\)((\s*:.+?\s*)?)(\s*{)/';

    private const METHOD_ARGUMENTS_INDEX = 3;

    private const METHOD_RETURN_TYPE = 4;

    private const AFTER_PATTERN = '/(return )(.+})(;)|(return)(\s.+?)(;)/s';

    private const METHOD_OVERRIDE = /** @lang InjectablePHP */
        'if($__fn__ = \AspectOverride\Facades\Instance::getOverwriteForClass(__CLASS__, __FUNCTION__)) { %s }';

    private const METHOD_ARGUMENTS_OVERRIDE = /** @lang InjectablePHP */
        'if($_fn__args = \AspectOverride\Facades\Instance::wrapAround(__CLASS__, __FUNCTION__, %s, ...func_get_args())) { extract($_fn__args); }';

    private const METHOD_AFTER_OVERRIDE = /** @lang InjectablePHP */
        '\AspectOverride\Facades\Instance::wrapReturn(__CLASS__, __FUNCTION__, %s)';

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
