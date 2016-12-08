<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class RemoveImportsVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\Use_
            || $node instanceof Stmt\GroupUse
        ) {
            return false;
        }
    }
}
