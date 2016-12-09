<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage ClassConstToStrVisitor
 * @author     Kamal Khan <shout@bhittani.com>
 * @version    1.x
 * @link       https://github.com/kamalkhan/php-parser
 * @copyright  2016 Kamal Khan
 * @license    https://github.com/kamalkhan/php-parser/blob/master/LICENSE
 */

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage ClassConstToStrVisitor
 */
class ClassConstToStrVisitor extends NodeVisitorAbstract
{
    /**
     * Namespace node
     * @var string|\PhpParser\Node\Name
     */
    protected $namespaceNode = '';

    /**
     * Use import nodes
     * @var array[\PhpParser\Node\Name]
     */
    protected $importNodes = [];

    /**
     * Class node
     * @var \PhpParser\Node\Name
     */
    protected $classNode;

    /**
     * Traverse a node when entering.
     * @param  \PhpParser\Node $node Traversing node
     * @return void
     */
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

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return \PhpParser\Node\Scalar\String_ Updated node
     */
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
