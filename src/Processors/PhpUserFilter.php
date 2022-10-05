<?php

namespace AspectOverride\Processors;

use AspectOverride\Facades\AspectOverride;

/**
 * Implementation heavily inspired from:
 * https://github.com/php-vcr/php-vcr/blob/master/src/VCR/CodeTransform/AbstractCodeTransform.php
 */
class PhpUserFilter extends \php_user_filter {
    public const NAME = 'aspect_mock_processor';

    /**
     * @return CodeProcessorInterface[]
     */
    public function getProcessors(): array {
        static $processors; // Can't use constructor since the object isn't constructed normally
        if(!$processors) {
            $processors = [new FunctionProcessor, new ClassMethodProcessor];
        }
        return $processors;
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
    public function filter($in, $out, &$consumed, $closing): int {
        while ($bucket = stream_bucket_make_writeable($in)) {
            /** @var \stdClass $bucket */
            foreach ($this->getProcessors() as $processor) {
                $bucket->data = $processor->transform($bucket->data);
            }
            $consumed += $bucket->datalen;
            AspectOverride::dump($bucket->data);
            stream_bucket_append($out, $bucket);
        }
        return \PSFS_PASS_ON;
    }

    /**
     * Attaches the current filter to a stream.
     */
    public function register(): void {
        if (!\in_array(static::NAME, stream_get_filters(), true)) {
            $isRegistered = stream_filter_register(static::NAME, static::class);
            if (!$isRegistered) {
                throw new \RuntimeException(sprintf('Failed registering stream filter "%s" on stream "%s"', static::class, static::NAME));
            }
        }
    }

    public function onNew(): void
    {
        foreach ($this->getProcessors() as $processor) {
            $processor->onNew();
        }
    }
}
