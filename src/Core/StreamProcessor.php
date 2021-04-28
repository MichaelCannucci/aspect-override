<?php


namespace AspectOverride\Core;


use AspectOverride\Transformers\ClassTransformer;
use AspectOverride\Transformers\FunctionOverrider;
use AspectOverride\Transformers\Visitors\BeforeFunctionVisitor;

class StreamProcessor
{
    /** @var ClassTransformer */
    protected $classTransformer;

    public function __construct()
    {
        $this->classTransformer = new ClassTransformer(new BeforeFunctionVisitor());
    }

    /**
     * @return resource|false
     */
    public function processOpen(string $path)
    {
        try {
            $shouldCache = \AspectOverride\Facades\Instance::getConfiguration()->shouldCache();
            $temporaryDirectory = \AspectOverride\Facades\Instance::getConfiguration()->getTemporaryFilesDirectory();
            $code = file_get_contents($path, true);
            if(false === $code) {
                throw new \Exception("Unable to get file contents");
            }
            if($shouldCache) {
                $hashed = hash("sha256", $code);
                $cachedPath = $temporaryDirectory . $hashed . ".php";
                if(!file_exists($cachedPath)) {
                    $this->saveToFileSystem($code, $cachedPath);
                }
                $resource = $this->loadFromFileSystem($cachedPath);
            } else {
                $resource = $this->toMemory($code);
            }
            if($namespace = $this->getStringBetween($code, "namespace", ";")) {
                FunctionOverrider::loadFunctions($namespace);
            }
            return $resource;
        } catch (\Exception $ignored) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return resource|false
     */
    protected function loadFromFileSystem(string $path)
    {
        return fopen($path, "r+");
    }

    protected function saveToFileSystem(string $originalCode, string $path): void
    {
        $transformed = $this->classTransformer->transform($originalCode);
        file_put_contents($path, $transformed);
    }

    /**
     * @param string $code
     * @return resource|false
     * @throws \Exception unable to create the resource
     */
    protected function toMemory(string $code)
    {
        $transformed = $this->classTransformer->transform($code);
        $resource = fopen('php://memory','r+');
        if(false === $resource) {
            return false;
        }
        fwrite($resource, $transformed);
        rewind($resource);
        return $resource;
    }

    /**
     * @return false|string
     */
    function getStringBetween(string $str, string $from, string $to)
    {
        $sub = substr($str, stripos($str,$from)+strlen($from),strlen($str));
        return substr($sub,0, stripos($sub,$to) ?: null);
    }
}