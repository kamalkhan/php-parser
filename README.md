PHP Parser [![Build Status](https://travis-ci.org/kamalkhan/php-parser.svg?branch=master)](https://travis-ci.org/kamalkhan/php-parser)
======
Syntax parser with back porting down to previous versions.

This library contains custom traversal visitors for use with [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser).

## Table of contents

- [Table of contents](#table-of-contents)
- [Install](#install)
- [Usage](#usage)
	- [Group import to individual imports](#group-import-to-individual-imports)
	- [Splat calls to call_user_func_array](#splat-calls-to-calluserfuncarray)
    - [Class constants to strings](#class-constants-to-strings)
	- [Variadic to func_get_args](#variadic-to-funcgetargs)
    - [Remove imports](#remove-imports)
	- [Append suffix](#append-suffix)
- [Test](#test)
- [Credits](#credits)
- [License](#license)

## Install

This library may be consumed by using [composer](https://getcomposer.org).

In your terminal, run:
```shell
$ composer require bhittani/php-parser
```

## Usage

To utilize this library make sure you understand how [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser/blob/2.x/doc/2_Usage_of_basic_components.markdown#node-traversation) parses code.

This library contains a set of node visitors to manipulate php code.

#### Group import to individual imports

Back ports php 7+ syntax code.

```php
use Bhittani\PhpParser\GroupToSingleImportsVisitor;

$traverser->addVisitor(new GroupToSingleImportsVisitor);
```

> which will convert

```php
use Grouped\Imports\\{Acme, Foo\Bar}
```

> into

```php
use Grouped\Imports\Acme
use Grouped\Imports\Foo\Bar
```

#### Splat calls to call_user_func_array

Back ports php 5.6+ syntax code.

```php
use Bhittani\PhpParser\SplatToCallUserFuncArrayVisitor;

$traverser->addVisitor(new SplatToCallUserFuncArrayVisitor);
```

> which will convert

```php
$val = my_func($a, 'b', ...$params);
```

> into

```php
$val = call_user_func_array('my_func', array_merge(array(
    $a, 'b'
), $params));
```

#### Class constants to strings

Back ports php 5.5+ syntax code.

```php
use Bhittani\PhpParser\ClassConstToStrVisitor;

$traverser->addVisitor(new ClassConstToStrVisitor);
```

> which will convert

```php
Acme\Foo::class
```

> into

```php
'Acme\Foo'
```

#### Variadic to func_get_args

Back ports php 5.6+ syntax code.

```php
use Bhittani\PhpParser\VariadicToFuncGetArgsVisitor;

$traverser->addVisitor(new VariadicToFuncGetArgsVisitor);
```

> which will convert

```php
function my_func($a, $b, ...$params)
{
    // my_func code
}
```

> into

```php
function my_func()
{
    $params = func_get_args();
    $a = array_shift($params);
    $b = array_shift($params);

    // my_func code
}
```

#### Remove imports

Removes all import statements.

```php
use Bhittani\PhpParser\RemoveImportsVisitor;

$traverser->addVisitor(new RemoveImportsVisitor);
```

> which will remove all `use` statements.

#### Append suffix

Appends a suffix to all imports, classes, traits, and interfaces.

```php
use Bhittani\PhpParser\AppendSuffixVisitor;

$traverser->addVisitor(new AppendSuffixVisitor('_1'));
```

> which will convert

```php
<?php

namespace Company\Package;

use Acme\Foo;
use Acme\Bar\{Beep, Boop};

interface Contract {}

abstract class AnAbstract {}

trait OurTrait {}

class Person extends AnAbstract implements Contract, AnotherContract
{
    use OurTrait;

    use TheirTrait;

    public function handle(Beep $beep, \Age $age)
    {
        $foo = new Foo();
        $bar = new Bar();
    }
}
```

> into

```php
<?php

namespace Company\Package;

use Acme\Foo_1 as Foo;
use Acme\Bar\{Beep_1 as Beep, Boop_1 as Boop};

interface Contract_1 {}

abstract class AnAbstract_1 {}

trait OurTrait_1 {}

class Person_1 extends AnAbstract_1 implements Contract_1, AnotherContract_1
{
    use OurTrait_1;

    use TheirTrait_1;

    public function handle(Beep $beep, \Age_1 $age)
    {
        $foo = new Foo();
        $bar = new Bar_1();
    }
}
```

## Test
Make sure you first CD into the library's root directory.

Do a composer install.
```shell
$ composer install
```
Run the tests.
```shell
$ vendor/bin/phpunit tests
```
or
```shell
$ composer test
```

## Credits

This library would not be possible without making use of [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser).

## License

This library is released under the [MIT License](https://github.com/kamalkhan/php-parser/blob/master/LICENSE).
