<?php

namespace AspectOverride\Processors;

class ClassMethodProcessor extends AbstractProcessor
{
    public const NAME = 'aspect_mock_method_override';

    private const PATTERN = '/([^use]\s+function\s+)(.+)(:.+|)({)(.+)([^\s])/sU';

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
        // Ackward way to place the override function at the start of the function
        // Using regex subsitutions to place the interception
        $transformed = preg_replace_callback(self::PATTERN, function ($m) {
            $return = (strpos($m[self::METHOD_RETURN_INDEX], 'void') === false) ? 'return $__fn__();' : '$__fn__(); return;';
            $template = sprintf(self::METHOD_OVERRIDE, $return);
            // Crude way of doing it, but we want our injection to be before the last capture group
            return $m[1] . $m[2] . $m[3] . $m[4] . $template . $m[5] . $m[6];
        }, $data, -1, $count, PREG_SET_ORDER);
        return $transformed;
    }
}
