<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\AppendSuffixVisitor as Visitor;

class AppendSuffixVisitorTest extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_and_append_a_suffix_to_classes_imports_and_fqcn_calls()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use ArrayAccess;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Boop as Baar;'.PHP_EOL;
        $code .= 'use \Model\User;'.PHP_EOL;
        $code .= 'use InvalidArgumentException;'.PHP_EOL;
        $code .= 'interface AppendSuffixInterface'.PHP_EOL;
        $code .= '{'.PHP_EOL.'}'.PHP_EOL;
        $code .= 'abstract class AppendSuffixAbstract'.PHP_EOL;
        $code .= '{'.PHP_EOL.'}'.PHP_EOL;
        $code .= 'trait AppendSuffixTrait'.PHP_EOL;
        $code .= '{'.PHP_EOL.'}'.PHP_EOL;
        $code .= 'class AppendSuffix extends AppendSuffixAbstract implements AppendSuffixInterface, Baz, Beep\Beeep, ArrayAccess'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    use AppendSuffixTrait;'.PHP_EOL;
        $code .= '    use Baz\BazTrait;'.PHP_EOL;
        $code .= '    use \Outside\Tr;'.PHP_EOL;
        $code .= '    public function add(Bar $bar, SomeClass $z)'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        $hello = \'hello\';'.PHP_EOL;
        $code .= '        $factory = new static();'.PHP_EOL;
        $code .= '        $static = ABSPATH;'.PHP_EOL;
        $code .= '        $zero = 0;'.PHP_EOL;
        $code .= '        $false = false;'.PHP_EOL;
        $code .= '        $FALSE = FALSE;'.PHP_EOL;
        $code .= '        $False = False;'.PHP_EOL;
        $code .= '        $true = true;'.PHP_EOL;
        $code .= '        $TRUE = TRUE;'.PHP_EOL;
        $code .= '        $True = True;'.PHP_EOL;
        $code .= '        $null = null;'.PHP_EOL;
        $code .= '        $NULL = NULL;'.PHP_EOL;
        $code .= '        $Null = Null;'.PHP_EOL;
        $code .= '        $arr = [];'.PHP_EOL;
        $code .= '        $eol = \'new\' . PHP_EOL . \'line\';'.PHP_EOL;
        $code .= '        require \'require.php\';'.PHP_EOL;
        $code .= '        $static = static::HELLO;'.PHP_EOL;
        $code .= '        $static = parent::HELLO;'.PHP_EOL;
        $code .= '        $static = self::HELLO;'.PHP_EOL;
        $code .= '        $static = static::$HELLO;'.PHP_EOL;
        $code .= '        $static = parent::$HELLO;'.PHP_EOL;
        $code .= '        $static = self::$HELLO;'.PHP_EOL;
        $code .= '        $static = static::hello();'.PHP_EOL;
        $code .= '        $STATIC = STATIC::hello();'.PHP_EOL;
        $code .= '        $Static = Static::hello();'.PHP_EOL;
        $code .= '        $self = self::hello();'.PHP_EOL;
        $code .= '        $SELF = SELF::hello();'.PHP_EOL;
        $code .= '        $Self = Self::hello();'.PHP_EOL;
        $code .= '        $parent = parent::hello();'.PHP_EOL;
        $code .= '        $PARENT = PARENT::hello();'.PHP_EOL;
        $code .= '        $Parent = Parent::hello();'.PHP_EOL;
        $code .= '        $a = new Box();'.PHP_EOL;
        $code .= '        $b = new Baar\Baar();'.PHP_EOL;
        $code .= '        $c = Bar::static;'.PHP_EOL;
        $code .= '        $d = Okay::static;'.PHP_EOL;
        $code .= '        new Beep();'.PHP_EOL;
        $code .= '        new Acme();'.PHP_EOL;
        $code .= '        $u = new User();'.PHP_EOL;
        $code .= '        $u->hell();'.PHP_EOL;
        $code .= '        array_shift([1, 2, 3]);'.PHP_EOL;
        $code .= '        throw new \Exception();'.PHP_EOL;
        $code .= '        throw new InvalidArgumentException();'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar_123 as Bar;'.PHP_EOL;
        $expected .= 'use ArrayAccess;'.PHP_EOL;
        $expected .= 'use Acme\Foo\{Bar\Baz_123 as Baz, Beep_123 as Beep};'.PHP_EOL;
        $expected .= 'use Acme\Boop_123 as Baar;'.PHP_EOL;
        $expected .= 'use Model\User_123 as User;'.PHP_EOL;
        $expected .= 'use InvalidArgumentException;'.PHP_EOL;
        $expected .= 'interface AppendSuffixInterface_123'.PHP_EOL;
        $expected .= '{'.PHP_EOL.'}'.PHP_EOL;
        $expected .= 'abstract class AppendSuffixAbstract_123'.PHP_EOL;
        $expected .= '{'.PHP_EOL.'}'.PHP_EOL;
        $expected .= 'trait AppendSuffixTrait_123'.PHP_EOL;
        $expected .= '{'.PHP_EOL.'}'.PHP_EOL;
        $expected .= 'class AppendSuffix_123 extends AppendSuffixAbstract_123 implements AppendSuffixInterface_123, Baz, Beep\Beeep_123, ArrayAccess'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    use AppendSuffixTrait_123;'.PHP_EOL;
        $expected .= '    use Baz\BazTrait_123;'.PHP_EOL;
        $expected .= '    use \Outside\Tr_123;'.PHP_EOL;
        $expected .= '    public function add(Bar $bar, SomeClass_123 $z)'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        $hello = \'hello\';'.PHP_EOL;
        $expected .= '        $factory = new static();'.PHP_EOL;
        $expected .= '        $static = ABSPATH;'.PHP_EOL;
        $expected .= '        $zero = 0;'.PHP_EOL;
        $expected .= '        $false = false;'.PHP_EOL;
        $expected .= '        $FALSE = FALSE;'.PHP_EOL;
        $expected .= '        $False = False;'.PHP_EOL;
        $expected .= '        $true = true;'.PHP_EOL;
        $expected .= '        $TRUE = TRUE;'.PHP_EOL;
        $expected .= '        $True = True;'.PHP_EOL;
        $expected .= '        $null = null;'.PHP_EOL;
        $expected .= '        $NULL = NULL;'.PHP_EOL;
        $expected .= '        $Null = Null;'.PHP_EOL;
        $expected .= '        $arr = [];'.PHP_EOL;
        $expected .= '        $eol = \'new\' . PHP_EOL . \'line\';'.PHP_EOL;
        $expected .= '        require \'require.php\';'.PHP_EOL;
        $expected .= '        $static = static::HELLO;'.PHP_EOL;
        $expected .= '        $static = parent::HELLO;'.PHP_EOL;
        $expected .= '        $static = self::HELLO;'.PHP_EOL;
        $expected .= '        $static = static::$HELLO;'.PHP_EOL;
        $expected .= '        $static = parent::$HELLO;'.PHP_EOL;
        $expected .= '        $static = self::$HELLO;'.PHP_EOL;
        $expected .= '        $static = static::hello();'.PHP_EOL;
        $expected .= '        $STATIC = STATIC::hello();'.PHP_EOL;
        $expected .= '        $Static = Static::hello();'.PHP_EOL;
        $expected .= '        $self = self::hello();'.PHP_EOL;
        $expected .= '        $SELF = SELF::hello();'.PHP_EOL;
        $expected .= '        $Self = Self::hello();'.PHP_EOL;
        $expected .= '        $parent = parent::hello();'.PHP_EOL;
        $expected .= '        $PARENT = PARENT::hello();'.PHP_EOL;
        $expected .= '        $Parent = Parent::hello();'.PHP_EOL;
        $expected .= '        $a = new Box_123();'.PHP_EOL;
        $expected .= '        $b = new Baar\Baar_123();'.PHP_EOL;
        $expected .= '        $c = Bar::static;'.PHP_EOL;
        $expected .= '        $d = Okay_123::static;'.PHP_EOL;
        $expected .= '        new Beep();'.PHP_EOL;
        $expected .= '        new Acme_123();'.PHP_EOL;
        $expected .= '        $u = new User();'.PHP_EOL;
        $expected .= '        $u->hell();'.PHP_EOL;
        $expected .= '        array_shift([1, 2, 3]);'.PHP_EOL;
        $expected .= '        throw new \Exception();'.PHP_EOL;
        $expected .= '        throw new InvalidArgumentException();'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor('_123'));
        $this->assertEquals($expected, $this->parse($code));
    }

    /** @test */
    public function it_should_traverse_and_append_a_suffix_via_regex_array()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Boop;'.PHP_EOL;
        $code .= 'class Skip'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    use ATrait;'.PHP_EOL;
        $code .= '    use \ATrait;'.PHP_EOL;
        $code .= '    use \Acme\Foo\BoopTrait;'.PHP_EOL;
        $code .= '    public function foo(Bar $bar, Boop $boop)'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        $a = new Baz(new Beep());'.PHP_EOL;
        $code .= '        $b = new Acme\Foo();'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar_1 as Bar;'.PHP_EOL;
        $expected .= 'use Acme\Foo\{Bar\Baz_X as Baz, Beep_X as Beep};'.PHP_EOL;
        $expected .= 'use Acme\Boop_Y as Boop;'.PHP_EOL;
        $expected .= 'class Skip_1'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    use ATrait_1;'.PHP_EOL;
        $expected .= '    use \ATrait;'.PHP_EOL;
        $expected .= '    use \Acme\Foo\BoopTrait_X;'.PHP_EOL;
        $expected .= '    public function foo(Bar $bar, Boop $boop)'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        $a = new Baz(new Beep());'.PHP_EOL;
        $expected .= '        $b = new Acme\Foo_X();'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor('_1', [
            '/^\\\?Acme\\\Foo/' => '_X',
            '/^\\\?Acme\\\Boop/' => '_Y',
        ]));
        $this->assertEquals($expected, $this->parse($code));
    }
}
