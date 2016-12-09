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
use PhpParser\Node\Param;
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage VariadicToFuncGetArgsVisitor
 */
class VariadicToFuncGetArgsVisitor extends NodeVisitorAbstract
{
    /**
     * Construct a func_get_args compatible version of a variadic function.
     * @param  array[\PhpParser\Node\Stmt]   $nodes   Function statement nodes
     * @param  array[\PhpParser\Node\Param]  $params  Function param nodes
     * @param  \PhpParser\Node\Param $vParam Function variadic param
     * @return \PhpParser\Node\Expr\Assign Assignment node
     */
    protected function addFuncGetArgs(array $nodes, array $params, Param $vParam)
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

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return null
     */
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
