<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

class AppendSuffixVisitor extends NodeVisitorAbstract
{
    protected $suffix = '';

    protected $importNodes = [];

    protected $namespaceNode = '';

    protected $cancelNext = false;

    protected $forceSuffix = false;

    public function __construct($suffix)
    {
        $this->suffix = $suffix;
    }

    protected function addSuffix(Name $class)
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

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->cancelNext = true;
            $this->namespaceNode = $node->name;
        } else if ($node instanceOf Stmt\GroupUse) {
            $this->cancelNext = true;
            foreach ($node->uses as $use) {
                $this->importNodes[] = Name::concat($node->prefix, $use->name);
            }
        } else if ($node instanceOf Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->importNodes[] = $use->name;
            }
        } else if ($node instanceOf Stmt\UseUse) {
            $this->forceSuffix = true;
        } else if ($node instanceOf Expr\FuncCall) {
            $this->cancelNext = true;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\Class_
            || $node instanceof Stmt\Interface_
            || $node instanceof Stmt\Trait_
        ) {
            $node->name = $node->name . $this->suffix;
        } else if ($node instanceof Name) {
            if (!$this->cancelNext) {
                return $this->addSuffix($node);
            }
            $this->cancelNext = false;
        } else if ($node instanceOf Stmt\Use_) {
            $this->forceSuffix = false;
        }
    }
}
