<?php

namespace AspectOverride\Mocking;

use AspectOverride\Core\Instance;
use AspectOverride\Core\Hasher;
use AspectOverride\Mocking\Visitors\OverrideFunctionVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Dumper;

class ClassMocker
{
  /** @var Hasher */
  protected $hasher;
  /** @var Parser */
  protected $parser;
  /** @var NodeTraverser */
  protected $traverser;
  /** @var Dumper */
  protected $dumper;

  public function __construct(
    NodeVisitorAbstract $visitor = null,
    Hasher $hasher = null,
    Dumper $dumper = null
  ) {
    $this->hasher = $hasher ?? new Hasher();
    $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    $this->dumper = $dumper ?? new Dumper();
    $this->traverser = new NodeTraverser();
    $this->traverser->addVisitor(new NameResolver);
    $this->traverser->addVisitor($visitor ?? new OverrideFunctionVisitor);
  }
  public function loadMocked(string $filePath): void
  {
    $code = file_get_contents($filePath);
    if(!$code) {
      throw new \RuntimeException("File unaccessible: {$filePath}");
    }
    $path = Instance::getInstance()->getTemporaryDirectory() . ltrim($filePath, '/');
    $path = $this->resolveDir($path);
    $cachedMarker = $path . '-' . $this->hasher->getHash($code);
    if(Instance::getInstance()->shouldUseCache() 
        && file_exists($path)
         && file_exists($cachedMarker)
    ) {
      $this->includeFile($path);
      return;
    }
    $ast = $this->parser->parse($code);
    if(!$ast) {
      throw new \RuntimeException("Unable to parse file: {$filePath}");
    }
    $ast = $this->traverser->traverse($ast);
    // Save the file then include it
    $dir = $this->resolveDir(pathinfo($path, PATHINFO_DIRNAME));
    if (!is_dir($dir)) {
      mkdir($dir, 0666, true);
    }
    file_put_contents($path, '<?php' . PHP_EOL . $this->dumper->prettyPrint($ast));
    // use another file with a hash for checking if a cache file is up to date
    if(Instance::getInstance()->shouldUseCache()) {
      file_put_contents($cachedMarker, '');
    }
    $this->includeFile($path);
  }
  protected function includeFile(string $path): void
  {
    // Avoid polluting the scope
    include $path;
  }
  function resolveDir($filename)
  {
    $filename = str_replace('//', '/', $filename);
    $parts = explode('/', $filename);
    $out = array();
    foreach ($parts as $part){
        if ($part == '.') continue;
        if ($part == '..') {
            array_pop($out);
            continue;
        }
        $out[] = $part;
    }
    return implode('/', $out);
  }
}
