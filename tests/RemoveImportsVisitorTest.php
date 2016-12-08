<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\RemoveImportsVisitor as Visitor;

class RemoveImportsVisitorTest extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_and_remove_single_and_grouped_imports()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $code .= 'use Acme\Bar;'.PHP_EOL;
        $code .= 'use Acme\Foo\{Bar\Baz, Beep};'.PHP_EOL;
        $code .= 'use Acme\Boop;'.PHP_EOL;
        $code .= 'class RemovedImports'.PHP_EOL;
        $code .= '{'.PHP_EOL.'}';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'namespace Bhittani\PhpParser\Test;'.PHP_EOL.PHP_EOL;
        $expected .= 'class RemovedImports'.PHP_EOL;
        $expected .= '{'.PHP_EOL.'}';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
