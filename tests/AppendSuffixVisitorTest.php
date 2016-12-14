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
