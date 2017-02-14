<?php

namespace Bhittani\PhpParser\Test;

use Acme\Bar_2 as Bar;
use ArrayAccess;
use Acme\Foo\{Bar\Baz, Beep as Bong};
use Acme\Boop as Baar;
use Model\User;
use InvalidArgumentException;
use Bhittani\PhpParser\Test\Ball_1 as Tester;
interface AppendSuffixInterface_1
{
}
abstract class AppendSuffixAbstract_1
{
}
trait AppendSuffixTrait_1
{
}
class AppendSuffix_1 extends AppendSuffixAbstract_1 implements AppendSuffixInterface_1, Baz, Bong\Beep, ArrayAccess
{
    use AppendSuffixTrait_1;
    use Baz\BazTrait;
    use \Outside\Tr;
    public function add(Bar $bar, SomeClass_1 $z)
    {
        $hello = 'hello';
        $factory = new static();
        $static = ABSPATH;
        $zero = 0;
        $false = false;
        $FALSE = FALSE;
        $False = False;
        $true = true;
        $TRUE = TRUE;
        $True = True;
        $null = null;
        $NULL = NULL;
        $Null = Null;
        $arr = [];
        $eol = 'new' . PHP_EOL . 'line';
        require 'require.php';
        $static = static::HELLO;
        $static = parent::HELLO;
        $static = self::HELLO;
        $static = static::$HELLO;
        $static = parent::$HELLO;
        $static = self::$HELLO;
        $static = static::hello();
        $STATIC = STATIC::hello();
        $Static = Static::hello();
        $self = self::hello();
        $SELF = SELF::hello();
        $Self = Self::hello();
        $parent = parent::hello();
        $PARENT = PARENT::hello();
        $Parent = Parent::hello();
        $a = new Box_1();
        $b = new Baar\Baar();
        $c = Bar::static;
        $d = Okay_1::static;
        new Bong();
        new Bong\Bonz();
        new Acme_1();
        $u = new User();
        $u->hell();
        array_shift([1, 2, 3]);
        throw new \Exception();
        throw new InvalidArgumentException();
        Tester::go();
        Tester\Goal::go();
    }
}