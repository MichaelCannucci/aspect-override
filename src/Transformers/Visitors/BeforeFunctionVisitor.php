<?php

namespace AspectOverride\Transformers\Visitors;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;

class BeforeFunctionVisitor extends NodeVisitorAbstract
{
    /** @var string */
    protected $namespacedClassName;

    public function enterNode(Node $node): Node
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
        if (!$this->namespacedClassName || !$node->stmts) {
            return;
        }
        $builder = new BuilderFactory();
        $callFunction = $builder->funcCall(
            $builder->var('__fn__'),
            array_map(function (Param $param) {
                return $param->var;
            }, $node->params)
        );
        $ifBody = $this->shouldReturnAValue($node) ?
            [ 'stmts' => [new Return_($callFunction)]] :
                [ 'stmts' => [ new Expression($callFunction), new Return_()]];
        $beforeHook = new If_(
            new Assign(
                $builder->var('__fn__'),
                $builder->funcCall($this->escape('\AspectOverride\Facades\Registry::getForClass'), [
                    $this->escape($this->namespacedClassName), (string)$node->name
                ])
            ),
            $ifBody
        );
        array_unshift($node->stmts, $beforeHook);
    }

    protected function shouldReturnAValue(ClassMethod $node): bool
    {
        $returnType = $node->returnType;
        if ($returnType instanceof Identifier) {
            return $returnType->name !== 'void';
        }
        return true;
    }

    protected function escape(string $class): string
    {
        return str_replace("/", "//", $class);
    }
}
