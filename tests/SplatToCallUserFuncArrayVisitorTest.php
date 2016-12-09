<?php

namespace Bhittani\PhpParser\Tests;

use Bhittani\PhpParser\SplatToCallUserFuncArrayVisitor as Visitor;

class SplatToCallUserFuncArrayVisitorTest extends AbstractTestCase
{
    /** @test */
    public function it_should_traverse_and_convert_splat_calls_to_call_user_func_array()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $code .= '(new Acme())->splatCall($a, $b, ...$params);'.PHP_EOL;
        $code .= 'Acme::splatCall($a, $b, ...$params);'.PHP_EOL;
        $code .= '$this->splatCall($a, $b, ...$params);'.PHP_EOL;
        $code .= '$user = new User();'.PHP_EOL;
        $code .= '$user->splatCall($a, \'b\', ...$params);'.PHP_EOL;
        $code .= 'splatCall($a, $b, ...$params);'.PHP_EOL;
        $code .= 'unsplatCall($foo, $bar, $params);';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $expected .= 'call_user_func_array(array(new Acme(), \'splatCall\'), array_merge(array($a, $b), $params));'.PHP_EOL;
        $expected .= 'call_user_func_array(array(Acme::class, \'splatCall\'), array_merge(array($a, $b), $params));'.PHP_EOL;
        $expected .= 'call_user_func_array(array($this, \'splatCall\'), array_merge(array($a, $b), $params));'.PHP_EOL;
        $expected .= '$user = new User();'.PHP_EOL;
        $expected .= 'call_user_func_array(array($user, \'splatCall\'), array_merge(array($a, \'b\'), $params));'.PHP_EOL;
        $expected .= 'call_user_func_array(\'splatCall\', array_merge(array($a, $b), $params));'.PHP_EOL;
        $expected .= 'unsplatCall($foo, $bar, $params);';

        $this->traverser->addVisitor(new Visitor);
        $this->assertEquals($expected, $this->parse($code));
    }

    /** @test */
    public function it_should_be_able_to_use_stringified_static_calls_via_constructor()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $code .= 'Acme::splatCall($a, $b, ...$params);';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $expected .= 'call_user_func_array(array(\'Foo\\\Bar\\\Acme\', \'splatCall\'), array_merge(array($a, $b), $params));';

        $this->traverser->addVisitor(new Visitor(true));
        $this->assertEquals($expected, $this->parse($code));
    }

    /** @test */
    public function it_should_be_able_to_use_stringified_static_calls_via_method()
    {
        $code = '<?php'.PHP_EOL.PHP_EOL;
        $code .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $code .= 'Acme::splatCall($a, $b, ...$params);';

        $expected = '<?php'.PHP_EOL.PHP_EOL;
        $expected .= 'use Foo\Bar\Acme;'.PHP_EOL;
        $expected .= 'call_user_func_array(array(\'Foo\\\Bar\\\Acme\', \'splatCall\'), array_merge(array($a, $b), $params));';

        $visitor = (new Visitor())->useStringifiedStaticCalls();
        $this->traverser->addVisitor($visitor);
        $this->assertEquals($expected, $this->parse($code));
    }
}
