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
     * Extra suffix regexes
     * @var array
     */
    protected $extras = [];

    /**
     * Use import nodes
     * @var array[\PhpParser\Node\Name]
     */
    protected $importNodes = [];

    /**
     * Namespace node
     * @var string|\PhpParser\Node\Name
     */
    protected $namespaceNode = '';

    /**
     * Cancel the next traversal node
     * @var boolean
     */
    protected $cancelNext = false;

    /**
     * Is an import traversal node
     * @var boolean
     */
    protected $isImport = false;

    /**
     * Are we in a group of imports
     * @var boolean|\PhpParser\Node\Name
     */
    protected $groupImport = false;

    /**
     * Set the suffix.
     * @param  string $suffix Suffix
     * @param  array $extras  Extra suffix regexes
     * @return void
     */
    public function __construct($suffix, $extras = [])
    {
        $this->suffix = $suffix;
        $this->extras = $extras;
    }

    protected function append(Name $name, $precheck = null)
    {
        $sanitized = $name;
        if ($this->groupImport) {
            $sanitized = Name::concat($this->groupImport, $name);
        }
        $sanitized = $sanitized->toString();
        $found = null;
        foreach ($this->extras as $regex => $suffix) {
            if (preg_match($regex, (!is_null($precheck) ? "{$precheck}\\" : '') . $sanitized)) {
                $found = new Name($name->toString() . $suffix);
            }
        }
        if (!is_null($found)) {
            return $found;
        }
        return new Name($name->toString() . $this->suffix);
    }

    /**
     * Append a suffix to the node.
     * @param  \PhpParse\Node\Name $class Name node
     * @return \PhpParse\Node\Name Appended node
     */
    protected function appendSuffix(Name $class)
    {
        if ($class->isFullyQualified()) {
            if (count($class->parts) == 1) {
                return $class;
            }
            return $this->append(new Name('\\' . $class->toString()));
        }
        if ($this->isImport) {
            if (!$this->groupImport && count($class->parts) == 1) {
                return $class;
            }
            return $this->append($class);
        }
        foreach ($this->importNodes as $import) {
            if (strcmp($import->getLast(), $class->toString()) === 0) {
                return $class;
            }
        }
        return $this->append($class);
    }

    public function beforeTraverse(array $nodes)
    {
        $this->importNodes = [];
        $this->namespaceNode = '';
        $this->cancelNext = false;
        $this->isImport = false;
        $this->groupImport = false;
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
            $this->groupImport = $node->prefix;
            foreach ($node->uses as $use) {
                $this->importNodes[] = Name::concat($node->prefix, $use->name);
            }
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->importNodes[] = $use->name;
            }
        } elseif ($node instanceof Stmt\UseUse) {
            $this->isImport = true;
        } elseif ($node instanceof Expr\FuncCall) {
            $this->cancelNext = true;
        } elseif ($node instanceof Expr\ConstFetch) {
            $this->cancelNext = true;
        } elseif ($node instanceof Expr\StaticCall) {
            $this->cancelNext = true;
        } elseif ($node instanceof Expr\StaticPropertyFetch) {
            $lc = strtolower($node->class->toString());
            if (in_array($lc, ['static', 'self', 'parent'])) {
                $this->cancelNext = true;
            }
        } elseif ($node instanceof Expr\ClassConstFetch) {
            $lc = strtolower($node->class->toString());
            if (in_array($lc, ['static', 'self', 'parent'])) {
                $this->cancelNext = true;
            }
        } elseif ($node instanceof Expr\New_) {
            if ($node->class && $node->class instanceof Name) {
                $lc = strtolower($node->class->toString());
                if (in_array($lc, ['static', 'self', 'parent'])) {
                    $this->cancelNext = true;
                }
            }
        }
    }

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return \PhpParser\Node\Name Updated node
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\Class_
            || $node instanceof Stmt\Interface_
            || $node instanceof Stmt\Trait_
        ) {
            if (empty($this->suffix) && !empty($this->namespaceNode)) {
                $node->name = $this->append(new Name($node->name), $this->namespaceNode);
            }
            else {
                $node->name = $node->name . $this->suffix;
            }
        } elseif ($node instanceof Name) {
            if (!$this->cancelNext) {
                return $this->appendSuffix($node);
            }
            $this->cancelNext = false;
        } elseif ($node instanceof Stmt\Use_) {
            $this->isImport = false;
        } elseif ($node instanceof Stmt\GroupUse) {
            $this->groupImport = false;
        }
    }
}
