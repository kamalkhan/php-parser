<?php

namespace Bhittani\PhpParser;

/**
 * This file is part of the Bhittani\PhpParser package.
 *
 * @package    Bhittani\PhpParser
 * @subpackage SplatToCallUserFuncArrayVisitor
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
use PhpParser\Node\Scalar;
use PhpParser\NodeVisitorAbstract;

/**
 * @package Bhittani\PhpParser
 * @subpackage SplatToCallUserFuncArrayVisitor
 */
class SplatToCallUserFuncArrayVisitor extends NodeVisitorAbstract
{
    /**
     * Class constants to stringified visitor.
     * @var \Bhittani\PhpParser\ClassConstToStrVisitor
     */
    protected $classConstToStrVisitor;

    /**
     * Use string version of static calls?
     * @var boolean
     */
    protected $useStringifiedStaticCalls = false;

    /**
     * Whether to use string version of static calls or not.
     * @param boolean $useStringifiedStaticCalls Use stringified static calls?
     */
    public function __construct($useStringifiedStaticCalls = false)
    {
        $this->classConstToStrVisitor = new ClassConstToStrVisitor;
        $this->useStringifiedStaticCalls = $useStringifiedStaticCalls;
    }

    /**
     * Get variables of the splat function call.
     * @param  array[\PhpParser\Node\Arg] $args Node arguments
     * @return array[\PhpParser\Node\Expr\Variable] Node variables
     */
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
            return;
        }
        return $variables;
    }

    /**
     * Splact function call to call_user_func_array.
     * @param  \PhpParser\Node                      $callee    Callee node
     * @param  array[\PhpParser\Node\Expr\Variable] $variables Node variables
     * @return \PhpParser\Node\Expr\FuncCall call_user_func_array node
     */
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

    /**
     * Use string version of static calls.
     * @return $this Allow chaining
     */
    public function useStringifiedStaticCalls()
    {
        $this->useStringifiedStaticCalls = true;
        return $this;
    }

    /**
     * Traverse a node when entering.
     * @param  \PhpParser\Node $node Traversing node
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($this->useStringifiedStaticCalls) {
            $this->classConstToStrVisitor->enterNode($node);
        }
    }

    /**
     * Traverse a node when leaving.
     * @param  \PhpParser\Node $node Traversing node
     * @return \PhpParser\Node\Expr\FuncCall call_user_func_array node
     */
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
        } elseif ($node instanceof Expr\MethodCall) {
            if ($node->var instanceof Expr\New_) {
                $var = $node->var;
            } elseif ($node->var instanceof Expr\Variable) {
                $var = new Expr\Variable($node->var->name);
            } else {
                return;
            }
            $callee = new Expr\Array_([$var,new Scalar\String_($node->name)]);
            return $this->splatToCallUserFuncArray($callee, $variables);
        } elseif ($node instanceof Expr\StaticCall) {
            $var = new Expr\ClassConstFetch($node->class, 'class');
            if ($this->useStringifiedStaticCalls) {
                $var = $this->classConstToStrVisitor->leaveNode($var);
            }
            $callee = new Expr\Array_([$var, new Scalar\String_($node->name)]);
            return $this->splatToCallUserFuncArray($callee, $variables);
        }
    }
}
