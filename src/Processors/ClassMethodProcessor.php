<?php

namespace AspectOverride\Processors;

class ClassMethodProcessor extends AbstractProcessor
{
    public const NAME = 'aspect_mock_method_override';

    private const PATTERN = '/(private|protected|public)?(\s+function\s+\S*\(.*?\)(\s*:.+\s*)?)(\s*{)/';

    private const METHOD_OVERRIDE = 'if' .
        '($__fn__ = \AspectOverride\Facades\Instance::getInstance()->getRegistry()->getForClass(__CLASS__, __FUNCTION__))' .
        '{ %s }';

    private const METHOD_RETURN_INDEX = 3;

    /**
     * 
     * Add the injection points for the monkey-patching
     * 
     * @param string $data 
     * @return string 
     */
    public function transform(string $data): string
    {
        // Awkward way to place the override function at the start of the function
        // Using regex substitutions to place the interception
        $transformed = preg_replace_callback(self::PATTERN, function ($m) {
            $returnType = $m[self::METHOD_RETURN_INDEX] ?? null;
            $return = $returnType && (strpos($returnType, 'void') !== false) ? '$__fn__(); return;' : 'return $__fn__();';
            $template = sprintf(self::METHOD_OVERRIDE, $return);
            // We want our injection to be after the matches
            return $m[0] . $template;
        }, $data, -1, $count, PREG_SET_ORDER);
        if(!$transformed) {
            throw new \RuntimeException("General failure in transforming php code");
        }
        return $transformed;
    }
}
