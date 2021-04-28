<?php

namespace AspectOverride\Transformers;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Printer;

class ClassTransformer
{
    /** @var Parser */
    protected $parser;
    /** @var NodeTraverser */
    protected $traverser;
    /** @var Printer */
    protected $dumper;

    public function __construct(
        NodeVisitorAbstract $visitor,
        Printer $dumper = null
    )
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->dumper = $dumper ?? new Printer();
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver);
        $this->traverser->addVisitor($visitor);
    }

    public function transform(string $code): string
    {
        $ast = $this->parser->parse($code);
        if (!$ast) {
            throw new \RuntimeException("Unable to parse code");
        }
        $ast = $this->traverser->traverse($ast);
        return '<?php' . PHP_EOL . $this->dumper->prettyPrint($ast);
    }
}