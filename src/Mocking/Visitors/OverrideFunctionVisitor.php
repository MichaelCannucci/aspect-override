<?php

namespace AspectOverride\Mocking\Visitors;

use AspectOverride\Util\ClassUtils;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;
use RuntimeException;

class OverrideFunctionVisitor extends NodeVisitorAbstract
{
  /** @var string */
  protected $namespacedClassName;

  public function enterNode(Node $node)
  {
    if ($node instanceof ClassMethod) {
      $this->handleFunction($node);
    } elseif ($node instanceof Class_) {
      $this->namespacedClassName = $node->namespacedName;
    }
    return $node;
  }
  protected function handleFunction(ClassMethod $node): void
  {
    if (!$this->namespacedClassName) {
      throw new RuntimeException("Expecting the function to be apart of a class: {$node->name}:{$node->getStartLine()}");
    }
    $builder = new BuilderFactory();
    $stmt = new If_(
      new Assign(
        $builder->var('__fn__'),
        $builder->funcCall($this->escape('\AspectOverride\Core\Registry::getForClass'), [
          $this->escape($this->namespacedClassName), (string)$node->name
        ])
      ),
      [
        'stmts' => [
          new Return_(
            $builder->funcCall(
              $builder->var('__fn__'),
              array_map(function (Param $param) {
                return $param->var;
              }, $node->params)
            )
          )
        ]
      ]
    );
    /** @phpstan-ignore-next-line */
    array_unshift($node->stmts, $stmt);
  }
  protected function escape(string $class): string
  {
    return str_replace("/", "//", $class);
  }
}
