<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\ClassConstToStrVisitor as Visitor;

class ClassConstToStrVisitor extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_class_constants_to_strings_when_namespaced()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Foo;'.PHP_EOL;
        $code .= 'class ClassConsts'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    public function foo()'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        $a = ClassConsts::class;'.PHP_EOL;
        $code .= '        $b = Bar::class;'.PHP_EOL;
        $code .= '        $c = Baz::class;'.PHP_EOL;
        $code .= '        $d = Beep::class;'.PHP_EOL;
        $code .= '        $e = Foo\Boop::class;'.PHP_EOL;
        $code .= '        $f = \Fqcn::class;'.PHP_EOL;
        $code .= '        $g = Emca\Acme::class;'.PHP_EOL;
        $code .= '        $h = static::class;'.PHP_EOL;
        $code .= '        $i = self::class;'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar;'.PHP_EOL;
        $expected .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $expected .= 'use Acme\Foo;'.PHP_EOL;
        $expected .= 'class ClassConsts'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    public function foo()'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        $a = \'Bhittani\\\PhpParser\\\Test\\\ClassConsts\';'.PHP_EOL;
        $expected .= '        $b = \'Acme\\\Bar\';'.PHP_EOL;
        $expected .= '        $c = \'Acme\\\Foo\\\Bar\\\Baz\';'.PHP_EOL;
        $expected .= '        $d = \'Acme\\\Foo\\\Beep\';'.PHP_EOL;
        $expected .= '        $e = \'Acme\\\Foo\\\Boop\';'.PHP_EOL;
        $expected .= '        $f = \'Fqcn\';'.PHP_EOL;
        $expected .= '        $g = \'Bhittani\\\PhpParser\\\Test\\\Emca\\\Acme\';'.PHP_EOL;
        $expected .= '        $h = \'Bhittani\\\PhpParser\\\Test\\\ClassConsts\';'.PHP_EOL;
        $expected .= '        $i = \'Bhittani\\\PhpParser\\\Test\\\ClassConsts\';'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }

    /** @test */
    public function it_should_traverse_class_constants_to_strings_when_not_namespaced()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Foo;'.PHP_EOL;
        $code .= 'class ClassConsts'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    public function foo()'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        $a = ClassConsts::class;'.PHP_EOL;
        $code .= '        $b = Bar::class;'.PHP_EOL;
        $code .= '        $c = Baz::class;'.PHP_EOL;
        $code .= '        $d = Beep::class;'.PHP_EOL;
        $code .= '        $e = Foo\Boop::class;'.PHP_EOL;
        $code .= '        $f = \Fqcn::class;'.PHP_EOL;
        $code .= '        $g = Emca\Acme::class;'.PHP_EOL;
        $code .= '        $h = static::class;'.PHP_EOL;
        $code .= '        $i = self::class;'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar;'.PHP_EOL;
        $expected .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $expected .= 'use Acme\Foo;'.PHP_EOL;
        $expected .= 'class ClassConsts'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    public function foo()'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        $a = \'ClassConsts\';'.PHP_EOL;
        $expected .= '        $b = \'Acme\\\Bar\';'.PHP_EOL;
        $expected .= '        $c = \'Acme\\\Foo\\\Bar\\\Baz\';'.PHP_EOL;
        $expected .= '        $d = \'Acme\\\Foo\\\Beep\';'.PHP_EOL;
        $expected .= '        $e = \'Acme\\\Foo\\\Boop\';'.PHP_EOL;
        $expected .= '        $f = \'Fqcn\';'.PHP_EOL;
        $expected .= '        $g = \'Emca\\\Acme\';'.PHP_EOL;
        $expected .= '        $h = \'ClassConsts\';'.PHP_EOL;
        $expected .= '        $i = \'ClassConsts\';'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
