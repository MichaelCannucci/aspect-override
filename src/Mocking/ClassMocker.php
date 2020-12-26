<?php

namespace AspectOverride\Mocking;

use AspectOverride\Core\Core;
use AspectOverride\Core\Hasher;
use AspectOverride\Mocking\Visitors\OverrideFunctionVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Dumper;

class ClassMocker implements  MockCreatorInterface
{
  /** @var Hasher */
  protected $hasher;
  /** @var Parser */
  protected $parser;
  /** @var Traverser */
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
    $path = $this->getCachedPath($filePath, $code);
    if(Core::getInstance()->shouldUseCache() && file_exists($path)) {
      $this->includeFile($path);
      return;
    }
    $ast = $this->parser->parse($code);
    $ast = $this->traverser->traverse($ast);
    file_put_contents($path, '<?php' . PHP_EOL . $this->dumper->prettyPrint($ast));
    $this->includeFile($path);
  }
  protected function getCachedPath(string $originalFilePath, string $code): string
  {
    $name = basename($originalFilePath);
    $hash = $this->hasher->getHash($code, $name);
    $tmpDir = Core::getInstance()->getTemporaryDirectory();
    return $tmpDir . $hash;
  }
  protected function includeFile($path)
  {
    include $path;
  }
}