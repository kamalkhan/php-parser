<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitor\NameResolver;

class AppendRegexVisitor extends NameResolver
{
    protected $regexes;

    protected $imports = [];

    protected $isImport = false;

    protected $isGroupImport = false;

    public function __construct(array $regexes)
    {
        $this->regexes = $regexes;
    }

    protected function append(Name $node, Name $against = null)
    {
        if (is_null($against)) {
            $against = $node;
        }
        $found = null;
        foreach ($this->regexes as $regex => $suffix) {
            if (preg_match($regex, $against->toString())) {
                $found = new Name($node->toString() . $suffix);
            }
        }
        if (!is_null($found)) {
            return $found;
        }
        return $node;
    }

    protected function isImported(Name $node)
    {
        foreach ($this->imports as $import) {
            $importString = $import->original->toString();
            if (stripos($node->toString(), $importString) === 0) {
                return ($import->alias ? $import->alias : $import->original->getLast())
                    . substr($node->toString(), strlen($importString));
            }
        }
        return false;
    }

    protected function isUnderNamespace(Name $node)
    {
        if (!$this->namespace) {
            return false;
        }
        return stripos($node->toString(), $this->namespace . '\\') === 0;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\GroupUse) {
            $this->isGroupImport = true;
            foreach ($node->uses as $use) {
                $name = Name::concat($node->prefix, $use->name);
                $inode = new Stmt\UseUse($name, $use->alias);
                $inode->original = new Name($name->toString());
                $this->imports[] = $inode;
            }
        } elseif ($node instanceof Stmt\UseUse) {
            if (!$this->isGroupImport) {
                $this->isImport = true;
                $node->original = new Name($node->name->toString());
                $this->imports[] = $node;
            }
        }

        parent::enterNode($node);
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
            || $node instanceof Stmt\Function_
        ) {
            $node->name = $this->append(
                new Name($node->name),
                Name::concat($this->namespace, $node->name)
            );
        } elseif ($node instanceof Name) {
            if ($node->toString() == $this->namespace) {
                return $node;
            }
            if ($this->isImport) {
                return $this->append($node);
            }
            if (($str = $this->isImported($node)) !== false) {
                return new Name($str);
            }
            if ($this->isUnderNamespace($node)) {
                $node = $this->append($node);
                return new Name(ltrim(substr($node->toString(), strlen($this->namespace)), '\\'));
            }
            return $this->append($node);
        }
    }
}
