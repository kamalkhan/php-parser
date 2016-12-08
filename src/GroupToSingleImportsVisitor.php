<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class GroupToSingleImportsVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceOf Stmt\GroupUse) {
            $nodes = [];
            foreach ($node->uses as $use) {
                $name = Name::concat($node->prefix, $use->name);
                $nodes[] = new Stmt\Use_([new Stmt\UseUse($name)]);
            }
            return $nodes;
        }
    }
}
