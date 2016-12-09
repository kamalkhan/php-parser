<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage VariadicToFuncGetArgsVisitor
 * @author     Kamal Khan <shout@bhittani.com>
 * @version    1.x
 * @link       https://github.com/kamalkhan/php-parser
 * @copyright  2016 Kamal Khan
 * @license    https://github.com/kamalkhan/php-parser/blob/master/LICENSE
 */

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
