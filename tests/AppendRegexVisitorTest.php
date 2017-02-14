<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\AppendRegexVisitor as Visitor;

class AppendRegexVisitorTest extends AbstractTestCase
{
    public function testTest()
    {
        $this->traverser->addVisitor(new Visitor([
            '/^\\\?Bhittani\\\PhpParser\\\Test\\\/' => '_1',
            '/^\\\?Acme\\\Bar/' => '_2',
        ]));
        $this->assertEquals(
            file_get_contents(__DIR__ . '/_output.php'),
            $this->parse(file_get_contents(__DIR__ . '/_input.php'))
        );
    }
}
