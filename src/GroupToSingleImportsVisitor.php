<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage GroupToSingleImportsVisitor
 * @author     Kamal Khan <shout@bhittani.com>
 * @version    1.x
 * @link       https://github.com/kamalkhan/php-parser
 * @copyright  2016 Kamal Khan
 * @license    https://github.com/kamalkhan/php-parser/blob/master/LICENSE
 */

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage GroupToSingleImportsVisitor
 */
class GroupToSingleImportsVisitor extends NodeVisitorAbstract
{
    /**
     * Traverse a node when entering.
     * @param  \PhpParser\Node $node Traversing node
     * @return \PhpParser\NodeTraverser Excuse the childrens
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
    }

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return array[\PhpParser\Node\Stmt\Use_] Array of import nodes
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\GroupUse) {
            $nodes = [];
            foreach ($node->uses as $use) {
                $name = Name::concat($node->prefix, $use->name);
                $nodes[] = new Stmt\Use_([new Stmt\UseUse($name)]);
            }
            return $nodes;
        }
    }
}
