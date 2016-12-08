<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\NodeVisitorAbstract;

class SplatToCallUserFuncArrayVisitor extends NodeVisitorAbstract
{
    protected $classConstToStrVisitor;

    protected $useStringifiedStaticCalls = false;

    public function __construct($useStringifiedStaticCalls = false)
    {
        $this->classConstToStrVisitor = new classConstToStrVisitor;
        $this->useStringifiedStaticCalls = $useStringifiedStaticCalls;
    }

    protected function getSplatCallVars($args)
    {
        $isSplatCall = false;
        $variables = [];
        foreach ($args as $arg) {
            if ($arg->unpack) {
                $isSplatCall = true;
            }
            $variables[] = $arg->value;
        }
        if (! $isSplatCall) {
            return null;
        }
        return $variables;
    }

    public function useStringifiedStaticCalls()
    {
        $this->useStringifiedStaticCalls = true;
        return $this;
    }

    public function enterNode(Node $node)
    {
        if ($this->useStringifiedStaticCalls) {
            $this->classConstToStrVisitor->enterNode($node);
        }
    }

    protected function splatToCallUserFuncArray($callee, $variables)
    {
        return new Expr\FuncCall(
            new Name('call_user_func_array'),
            [
                $callee,
                new Expr\FuncCall(
                    new Name('array_merge'),
                    $variables
                )
            ]
        );
    }

    public function leaveNode(Node $node)
    {
        // Collect arguments as variables
        if ($node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
        ) {
            $variables = $this->getSplatCallVars($node->args);
            if (is_null($variables)) {
                return;
            }
        }

        // Call to call_user_func_array
        if ($node instanceof Expr\FuncCall) {
            $callee = new Scalar\String_($node->name);
            return $this->splatToCallUserFuncArray($callee, $variables);
        } else if ($node instanceof Expr\MethodCall) {
            if ($node->var instanceof Expr\New_) {
                $var = $node->var;
            } else if ($node->var instanceof Expr\Variable) {
                $var = new Expr\Variable($node->var->name);
            } else {
                return;
            }
            $callee = new Expr\Array_([$var,new Scalar\String_($node->name)]);
            return $this->splatToCallUserFuncArray($callee, $variables);
        } else if ($node instanceof Expr\StaticCall) {
            $var = new Expr\ClassConstFetch($node->class, 'class');
            if ($this->useStringifiedStaticCalls) {
                $var = $this->classConstToStrVisitor->leaveNode($var);
            }
            $callee = new Expr\Array_([$var, new Scalar\String_($node->name)]);
            return $this->splatToCallUserFuncArray($callee, $variables);
        }
    }
}
