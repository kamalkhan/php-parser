<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\VariadicToFuncGetArgsVisitor as Visitor;

class VariadicToFuncGetArgsVisitorTest extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_and_convert_variadic_funcs_to_normal_funcs()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'function variadicFunc1($a, $b, ...$params)'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    $c = \'Hello World!\';'.PHP_EOL;
        $code .= '    $foo = function ($foo, ...$params) {'.PHP_EOL;
        $code .= '        $z = \'Bye World!\';'.PHP_EOL;
        $code .= '    };'.PHP_EOL;
        $code .= '}'.PHP_EOL;
        $code .= '$foo = function ($foo, ...$params) {'.PHP_EOL;
        $code .= '    $z = \'Bye World!\';'.PHP_EOL;
        $code .= '};';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'function variadicFunc1()'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    $params = func_get_args();'.PHP_EOL;
        $expected .= '    $a = array_shift($params);'.PHP_EOL;
        $expected .= '    $b = array_shift($params);'.PHP_EOL;
        $expected .= '    $c = \'Hello World!\';'.PHP_EOL;
        $expected .= '    $foo = function () {'.PHP_EOL;
        $expected .= '        $params = func_get_args();'.PHP_EOL;
        $expected .= '        $foo = array_shift($params);'.PHP_EOL;
        $expected .= '        $z = \'Bye World!\';'.PHP_EOL;
        $expected .= '    };'.PHP_EOL;
        $expected .= '}'.PHP_EOL;
        $expected .= '$foo = function () {'.PHP_EOL;
        $expected .= '    $params = func_get_args();'.PHP_EOL;
        $expected .= '    $foo = array_shift($params);'.PHP_EOL;
        $expected .= '    $z = \'Bye World!\';'.PHP_EOL;
        $expected .= '};';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
