<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage ImportsVisitor
 * @author     Kamal Khan <shout@bhittani.com>
 * @version    1.x
 * @link       https://github.com/kamalkhan/php-parser
 * @copyright  2017 Kamal Khan
 * @license    https://github.com/kamalkhan/php-parser/blob/master/LICENSE
 */

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage ImportsVisitor
 */
class ImportsVisitor extends NodeVisitorAbstract
{
    protected $imports = [];

    protected $namespace;

    protected $newImports = [];

    protected $isImport = false;

    protected $isGroupImport = false;

    protected function isImported(Name $node)
    {
        foreach ($this->imports as $import) {
            if ($node->getFirst() === $import->alias) {
                $tmp = new Name($import->name);
                array_pop($tmp->parts);
                return Name::concat($tmp, $node);
            }
        }
        return false;
    }

    protected function underNamespace(Name $node)
    {
        if (!$this->namespace) {
            return false;
        }
        return Name::concat($this->namespace, $node);
    }

    protected function process(Node $node)
    {
        if ($node->isFullyQualified()) {
            $this->newImports[] = new Stmt\Use_([new Name($node)]);
            return new Name($node->getLast());
        }
        if ($node->isQualified()) {
            if ($n = $this->isImported($node)) {
                $this->newImports[] = new Stmt\Use_([$n]);
                return new Name($node->getLast());
            }
            if ($n = $this->underNamespace($node)) {
                $this->newImports[] = new Stmt\Use_([$n]);
                return new Name($node->getLast());
            }
            $this->newImports[] = new Stmt\Use_([$node]);
            return new Name($node->getLast());
        }
        return $node;
    }

	public function beforeTraverse(array $nodes)
	{
		$this->imports = [];

	    $this->namespace = null;

	    $this->newImports = [];

	    $this->isImport = false;

	    $this->isGroupImport = false;
	}

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->namespace = $node->name->toString();
        } elseif ($node instanceof Stmt\GroupUse) {
            $this->isGroupImport = true;
            foreach ($node->uses as $use) {
                $name = Name::concat($node->prefix, $use->name);
                $inode = new Stmt\UseUse($name, $use->alias);
                $this->imports[] = $inode;
            }
        } elseif ($node instanceof Stmt\UseUse) {
            if (!$this->isGroupImport) {
                $this->isImport = true;
                $this->imports[] = $node;
            }
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\GroupUse) {
            $this->isGroupImport = false;
        } elseif ($node instanceof Stmt\UseUse) {
            if (!$this->isGroupImport) {
                $this->isImport = false;
            }
        } elseif ($node instanceof Stmt\Class_
            || $node instanceof Stmt\Interface_
            || $node instanceof Stmt\Trait_
        ) {
			if ($node->extends) {
				$node->extends = $this->process($node->extends);
			}
			if ($node instanceof Stmt\Class_ && $node->implements) {
				foreach ($node->implements as & $implement) {
	                $implement = $this->process($implement);
	            }
			}
        } elseif ($node instanceof Name && !$this->isImport) {
            if ($node->toString() == $this->namespace) {
                return $node;
            }
            return $this->process($node);
        }
    }

    public function afterTraverse(array $nodes)
    {
        if ($nodes[0] instanceof Stmt\Namespace_) {
            $nodes[0]->stmts = array_merge($this->newImports, $nodes[0]->stmts);
        } else {
            $nodes = array_merge($this->newImports, $nodes);
        }
        return $nodes;
    }
}
