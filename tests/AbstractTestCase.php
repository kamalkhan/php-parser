<?php

namespace Bhittani\PhpParser\Tests;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected $traverser;

    public function setUp()
    {
        $this->traverser = new NodeTraverser;
    }

    protected function parse($code)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse($code);
        $stmts = $this->traverser->traverse($stmts);
        return (new Standard)->prettyPrintFile($stmts);
    }
}
