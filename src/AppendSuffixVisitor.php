<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage AppendSuffixVisitor
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
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage AppendSuffixVisitor
 */
class AppendSuffixVisitor extends NodeVisitorAbstract
{
    /**
     * Suffix to append
     * @var string
     */
    protected $suffix = '';

    /**
     * Use import nodes
     * @var array[\PhpParser\Node\Name]
     */
    protected $importNodes = [];

    /**
     * Namespace node
     * @var string|array[\PhpParser\Node\Name]
     */
    protected $namespaceNode = '';

    /**
     * Cancel the next traversal node
     * @var boolean
     */
    protected $cancelNext = false;

    /**
     * Force the next traversal node
     * @var boolean
     */
    protected $forceSuffix = false;

    /**
     * Set the suffix.
     * @param string $suffix Suffix
     * @return void
     */
    public function __construct($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * Append a suffix to the node.
     * @param  \PhpParse\Node\Name $class Name node
     * @return \PhpParse\Node\Name Appended node
     */
    protected function append(Name $class)
    {
        $suffix = $this->suffix;
        if ($class->isFullyQualified()) {
            return new Name('\\' . $class->toString() . $suffix);
        }
        // Use force for use import statements.
        if ($this->forceSuffix || $class->isQualified()) {
            return new Name($class->toString() . $suffix);
        }
        foreach ($this->importNodes as $import) {
            if (strcmp($import->getLast(), $class->toString()) === 0) {
                return $class;
            }
        }
        return new Name($class->toString() . $suffix);
    }

    /**
     * Traverse a node when entering.
     * @param  \PhpParser\Node $node Traversing node
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->cancelNext = true;
            $this->namespaceNode = $node->name;
        } elseif ($node instanceof Stmt\GroupUse) {
            $this->cancelNext = true;
            foreach ($node->uses as $use) {
                $this->importNodes[] = Name::concat($node->prefix, $use->name);
            }
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->importNodes[] = $use->name;
            }
        } elseif ($node instanceof Stmt\UseUse) {
            $this->forceSuffix = true;
        } elseif ($node instanceof Expr\FuncCall) {
            $this->cancelNext = true;
        }
    }

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return \PhpParser\Node\Name|null Updated node
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\Class_
            || $node instanceof Stmt\Interface_
            || $node instanceof Stmt\Trait_
        ) {
            $node->name = $node->name . $this->suffix;
        } elseif ($node instanceof Name) {
            if (!$this->cancelNext) {
                return $this->append($node);
            }
            $this->cancelNext = false;
        } elseif ($node instanceof Stmt\Use_) {
            $this->forceSuffix = false;
        }
    }
}
