<?php

namespace Bhittani\PhpParser\Test;

use Acme\Bar;
use ArrayAccess;
use Acme\Foo\{Bar\Baz, Beep as Bong};
use Acme\Boop as Baar;
use \Model\User;
use InvalidArgumentException;
use Bhittani\PhpParser\Test\Ball as Tester;
interface AppendSuffixInterface
{
}
abstract class AppendSuffixAbstract
{
}
trait AppendSuffixTrait
{
}
class AppendSuffix extends AppendSuffixAbstract implements AppendSuffixInterface, Baz, Bong\Beep, ArrayAccess
{
    use AppendSuffixTrait;
    use Baz\BazTrait;
    use \Outside\Tr;
    public function add(Bar $bar, SomeClass $z)
    {
        Tester::go();
        Tester\Goal::go();
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
        $a = new Box();
        $b = new Baar\Baar();
        $c = Bar::static;
        $d = Okay::static;
        new Bong();
        new Bong\Bonz();
        new Acme();
        $u = new User();
        $u->hell();
        array_shift([1, 2, 3]);
        throw new \Exception();
        throw new InvalidArgumentException();
    }
}
