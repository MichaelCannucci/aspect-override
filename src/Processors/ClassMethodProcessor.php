<?php


namespace AspectOverride\Processors;

/**
 * Implementation heavily inspired from:
 * https://github.com/php-vcr/php-vcr/blob/master/src/VCR/CodeTransform/AbstractCodeTransform.php
 */
class ClassMethodProcessor extends \php_user_filter
{
    public const NAME = 'aspect_mock_source_code';

    private const PATTERN = '/(public|private|protected)( function )(.+)(:.+|)({)(.+)(\w|\d|\$)/sU';

    private const METHOD_OVERRIDE = 'if' .
        '($__fn__ = \AspectOverride\Facades\Registry::getForClass(__CLASS__, __FUNCTION__))' .
        '{ %s }';

    private const METHOD_RETURN_INDEX = 3;

    /**
     * Attaches the current filter to a stream.
     */
    public function register(): void
    {
        if (!\in_array(static::NAME, stream_get_filters(), true)) {
            $isRegistered = stream_filter_register(static::NAME, static::class);
            if (!$isRegistered) {
                throw new \RuntimeException(sprintf('Failed registering stream filter "%s" on stream "%s"', static::class, static::NAME));
            }
        }
    }

    /**
     * Applies the current filter to a provided stream.
     *
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int PSFS_PASS_ON
     *
     * @see http://www.php.net/manual/en/php-user-filter.filter.php
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = $this->transform($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }

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
        $a = preg_replace_callback(self::PATTERN, function ($m) {
            $return = (strpos($m[self::METHOD_RETURN_INDEX], 'void') === false) ? 'return $__fn__();' : '$__fn__(); return;';
            $template = sprintf(self::METHOD_OVERRIDE, $return);
            // Crude way of doing it, but we want our injection to be before the last capture group
            return $m[1] . $m[2] . $m[3] . $m[4] . $m[5] . $m[6] . $template . $m[7];
        }, $data, -1, $count,);
        return $a;
    }
}
