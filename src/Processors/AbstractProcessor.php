<?php

namespace AspectOverride\Processors;

/**
 * Implementation heavily inspired from:
 * https://github.com/php-vcr/php-vcr/blob/master/src/VCR/CodeTransform/AbstractCodeTransform.php
 */
abstract class AbstractProcessor extends \php_user_filter
{
    public const NAME = 'aspect_mock_processor';

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
    public function filter($in, $out, &$consumed, $closing): int
    {
        $this->onNewFile();
        while ($bucket = stream_bucket_make_writeable($in)) {
            /** @var \stdClass $bucket */
            $bucket->data = $this->transform($this->removeComments($bucket->data));
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }

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
     * Comments might be picked up the code transformers and produce invalid results
     */
    private function removeComments(string $data): string {
        return preg_replace('/(\/\/|#).+/', '//', $data);
    }

    public abstract function transform(string $data): string;

    public abstract function onNewFile(): void;
}
