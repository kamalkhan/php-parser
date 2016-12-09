<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\NodeVisitorAbstract;

class ClassConstToStrVisitor extends NodeVisitorAbstract
{
    protected $namespaceNode = '';

    protected $importNodes = [];

    protected $classNode;

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->namespaceNode = $node->name;
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->importNodes[] = Name::concat($node->prefix, $use->name);
            }
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->importNodes[] = $use->name;
            }
        } elseif ($node instanceof Stmt\Class_) {
            $this->classNode = $node;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Expr\ClassConstFetch
            && $node->name === 'class'
        ) {
            $class = $node->class;
            if (strcmp($class->toString(), 'static') === 0
                || strcmp($class->toString(), 'self') === 0
            ) {
                $name = Name::concat($this->namespaceNode, $this->classNode->name);
                return new Scalar\String_(trim($name->toString(), '\\'));
                // return new Expr\FuncCall(new Name('get_class'));
            }
            if ($class->isFullyQualified()) {
                return new Scalar\String_(trim($class->toString(), '\\'));
            }
            $prefix = $this->namespaceNode;
            foreach ($this->importNodes as $import) {
                if (strcmp($class->getFirst(), $import->getLast()) === 0) {
                    $prefix = $import->toString();
                    array_shift($class->parts);
                    break;
                }
            }
            $name = Name::concat($prefix, $class);
            return new Scalar\String_(trim($name->toString(), '\\'));
        }
    }
}
