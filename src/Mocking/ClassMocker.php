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
    $path = $this->getCachedPath($filePath, $code);
    if(Instance::getInstance()->getConfiguration()->getUseCache() && file_exists($path)) {
      $this->includeFile($path);
      return;
    }
    file_put_contents($path, $this->transform($code));
    $this->includeFile($path);
  }
  public function transform(string $code): string
  {
    $ast = $this->parser->parse($code);
    if(!$ast) {
      throw new \RuntimeException("Unable to parse code");
    }
    $ast = $this->traverser->traverse($ast);
    return '<?php' . PHP_EOL . $this->dumper->prettyPrint($ast);
  }
  protected function getCachedPath(string $originalFilePath, string $code): string
  {
    $name = basename($originalFilePath);
    $hash = $this->hasher->getHash($code, $name);
    $tmpDir = Instance::getInstance()->getConfiguration()->getTemporaryFilesDirectory();
    return $tmpDir . $hash;
  }
  protected function includeFile(string $path): void
  {
    include $path;
  }
}