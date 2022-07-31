<?php

namespace AspectOverride\Processors;

class ClassMethodProcessor extends AbstractProcessor {
    public const NAME = 'aspect_mock_method_override';

    private const BEFORE_PATTERN = '/(private|protected|public)?(\s+function\s+\S*)\(([\s\S]*?)\)((\s*:.+?\s*)?)(\s*{)/';

    private const METHOD_ARGUMENTS_INDEX = 3;

    private const METHOD_RETURN_TYPE = 4;

    private const AFTER_PATTERN = '/(return )(.+})(;)|(return)(\s.+?)(;)/s';

    private const METHOD_OVERRIDE = /** @lang InjectablePHP */
        'if($__fn__ = \AspectOverride\Facades\Instance::getOverwriteForClass(__CLASS__, __FUNCTION__)) { %s }';

    private const METHOD_ARGUMENTS_OVERRIDE = /** @lang InjectablePHP */
        'if($_fn__args = \AspectOverride\Facades\Instance::wrapArguments(__CLASS__, __FUNCTION__, %s, ...func_get_args())) { extract($_fn__args); }';

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
        // After has to come first or else we end up writing over the 'before' transformation
        return $this->beforeTransform($this->afterTransform($data));
    }

    protected function beforeTransform(string $data): string {
        $overwriteTransform = preg_replace_callback(self::BEFORE_PATTERN, function ($m) {
            // Arguments Overwrite
            $arguments = $m[self::METHOD_ARGUMENTS_INDEX];
            preg_match_all('/\$(.+?),?/', $arguments, $matches);
            $arguments = array_map(function ($x) {
                return $x;
            }, $matches[1]);
            $argumentOverwrite = sprintf(self::METHOD_ARGUMENTS_OVERRIDE, "['" . implode("','", $arguments) . "']");
            // Method Overwrite
            $returnType = $m[self::METHOD_RETURN_TYPE] ?? null;
            $return = $returnType && (strpos($returnType, 'void') !== false) ? '$__fn__(...func_get_args()); return;' : 'return $__fn__(...func_get_args());';
            $overwrite = sprintf(self::METHOD_OVERRIDE, $return);
            // We want our injection to be after the matches
            return $m[0] . $argumentOverwrite . ' ' . $overwrite;
        }, $data);
        if (!$overwriteTransform) {
            $this->failedTransform();
        }
        return $overwriteTransform;
    }

    protected function afterTransform(string $data): string {
        $afterTransform = preg_replace_callback(self::AFTER_PATTERN, function ($m) {
            // condition regex has entries for other pattern as blank strings, filtering it to 'normalize' it
            $m = array_values(array_filter($m));
            // Return (After Code) ;
            return $m[1] . ' ' . sprintf(self::METHOD_AFTER_OVERRIDE, $m[2]) . $m[3];
        }, $data);
        if (!$afterTransform) {
            $this->failedTransform();
        }
        return $afterTransform;
    }

    private function failedTransform() {
        throw new \RuntimeException("General failure in transforming php code");
    }

    public function onNewFile(): void {
    }
}
