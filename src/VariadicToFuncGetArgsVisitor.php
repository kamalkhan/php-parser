<?php

namespace Bhittani\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

class VariadicToFuncGetArgsVisitor extends NodeVisitorAbstract
{
    protected function addFuncGetArgs($nodes, $params, $vParam)
    {
        $newNodes = [];
        $newNodes[] = new Expr\Assign(
            new Expr\Variable($vParam->name),
            new Expr\FuncCall(new Name('func_get_args'))
        );
        foreach ($params as $param) {
            $newNodes[] = new Expr\Assign(
                new Expr\Variable($param->name),
                new Expr\FuncCall(
                    new Name('array_shift'),
                    [new Expr\Variable($vParam->name)]
                )
            );
        }
        return array_merge($newNodes, $nodes);
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            foreach ($node->params as $param) {
                if ($param->variadic) {
                    $vParam = array_pop($node->params);
                    $node->stmts = $this->addFuncGetArgs(
                        $node->stmts,
                        $node->params,
                        $vParam
                    );
                    $node->params = [];
                    return;
                }
            }
        }
    }
}
