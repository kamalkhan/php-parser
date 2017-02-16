<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\RelativeToImportVisitor as Visitor;

class RelativeToImportTest extends AbstractTestCase
{
    /** @test */
    public function it_should_convert_relative_to_imported_access()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Foo;'.PHP_EOL;
        $code .= 'class RelativeImports extends \Acme\Bar implements \Acme\Beep, Foo, Baz'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    public function __construct()'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        null;'.PHP_EOL;
        $code .= '        new \Acme\Boop();'.PHP_EOL;
        $code .= '        new Foo\Fee();'.PHP_EOL;
        $code .= '        new Hello\World\Bye();'.PHP_EOL;
        $code .= '        new \Bye\World\Hello();'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar;'.PHP_EOL;
        $expected .= 'use Acme\Beep;'.PHP_EOL;
        $expected .= 'use Acme\Boop;'.PHP_EOL;
        $expected .= 'use Acme\Foo\Fee;'.PHP_EOL;
        $expected .= 'use Bhittani\PhpParser\Test\Hello\World\Bye;'.PHP_EOL;
        $expected .= 'use Bye\World\Hello;'.PHP_EOL;
        $expected .= 'use Acme\Foo;'.PHP_EOL;
        $expected .= 'class RelativeImports extends Bar implements Beep, Foo, Baz'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    public function __construct()'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        null;'.PHP_EOL;
        $expected .= '        new Boop();'.PHP_EOL;
        $expected .= '        new Fee();'.PHP_EOL;
        $expected .= '        new Bye();'.PHP_EOL;
        $expected .= '        new Hello();'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }

    /** @test */
    public function it_should_convert_relative_to_imported_access_wo_namespace()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Foo;'.PHP_EOL;
        $code .= 'class RelativeImports extends \Acme\Bar implements \Acme\Beep, Foo, Baz'.PHP_EOL;
        $code .= '{'.PHP_EOL;
        $code .= '    public function __construct()'.PHP_EOL;
        $code .= '    {'.PHP_EOL;
        $code .= '        null;'.PHP_EOL;
        $code .= '        new \Acme\Boop();'.PHP_EOL;
        $code .= '        new Foo\Fee();'.PHP_EOL;
        $code .= '        new Hello\World\Bye();'.PHP_EOL;
        $code .= '        new \Bye\World\Hello();'.PHP_EOL;
        $code .= '    }'.PHP_EOL;
        $code .= '}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar;'.PHP_EOL;
        $expected .= 'use Acme\Beep;'.PHP_EOL;
        $expected .= 'use Acme\Boop;'.PHP_EOL;
        $expected .= 'use Acme\Foo\Fee;'.PHP_EOL;
        $expected .= 'use Hello\World\Bye;'.PHP_EOL;
        $expected .= 'use Bye\World\Hello;'.PHP_EOL;
        $expected .= 'use Acme\Foo;'.PHP_EOL;
        $expected .= 'class RelativeImports extends Bar implements Beep, Foo, Baz'.PHP_EOL;
        $expected .= '{'.PHP_EOL;
        $expected .= '    public function __construct()'.PHP_EOL;
        $expected .= '    {'.PHP_EOL;
        $expected .= '        null;'.PHP_EOL;
        $expected .= '        new Boop();'.PHP_EOL;
        $expected .= '        new Fee();'.PHP_EOL;
        $expected .= '        new Bye();'.PHP_EOL;
        $expected .= '        new Hello();'.PHP_EOL;
        $expected .= '    }'.PHP_EOL;
        $expected .= '}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
