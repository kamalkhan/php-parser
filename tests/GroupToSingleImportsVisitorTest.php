<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\GroupToSingleImportsVisitor as Visitor;

class GroupToSingleImportsVisitorTest extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_grouped_imports_into_seperate_imports()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Boop;'.PHP_EOL;
        $code .= 'class GroupedImports'.PHP_EOL;
        $code .= '{'.PHP_EOL.'}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'use Acme\Bar;'.PHP_EOL;
        $expected .= 'use Acme\Foo\Bar\Baz;'.PHP_EOL;
        $expected .= 'use Acme\Foo\Beep;'.PHP_EOL;
        $expected .= 'use Acme\Boop;'.PHP_EOL;
        $expected .= 'class GroupedImports'.PHP_EOL;
        $expected .= '{'.PHP_EOL.'}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
