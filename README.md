# ClassFinder
Find all classes from composer
<div id="top"></div>

<!-- TABLE OF CONTENTS -->
<details><summary>Table of Contents</summary>
  <ol>
    <li><a href="#installation">Installation</a></li><li>
        <a href="#usage">Usage</a>
        <ol>
            <li><a href="#getting_classes">Get classes</a></li>
            <li><a href="#filtering">Filter classes</a></li>
            <li><a href="#using_callbacks">Filter classes by self</a></li>
            <li><a href="#easy_methods">Easy Methods</a></li>
        </ol>
    </li>
  </ol>
</details>

## Installation

Install the package using composer:

```shell
composer require ahjdev/class-finder
```

<p align="right">(<a href="#top">back to top</a>)</p>

## Usage

- Create an Instance of `ClassFinder` (if you need to set path of `vendor` folder do it in `__construct`)
```php
<?php
use AhjDev\ClassFinder\ClassFinder;

$finder = new ClassFinder();
$finder = new ClassFinder('dir/to/vendor');
```
### Getting Classes
- You can use `getClasses` method to get classes from namespace:
```php
<?php
use AhjDev\ClassFinder\ClassFinder;

$finder = new ClassFinder;
$finder->getClasses('AhjDev');
```
### Filtering

- Filter result with FindType enum wich classes should return :
```php
<?php

namespace AhjDev\ClassFinder;

enum FindType
{
    const INTERFACE = 1;
    const TRAIT     = 2;
    const ENUM      = 4;
    const FINAL     = 8;
    const ABSTRACT  = 16;
    const SIMPLE_CLASS  = 32;
    const CLASSES   = self::FINAL | self::ABSTRACT | self::SIMPLE_CLASS;
    const ALL       = self::INTERFACE | self::TRAIT | self::ENUM | self::CLASSES;
}
```

```php
<?php
use AhjDev\ClassFinder\FindType;
use AhjDev\ClassFinder\ClassFinder;

$finder = new ClassFinder;
$finder->getClasses(
    'AhjDev',
    FindType::TRAIT | FindType::INTERFACE
);
```
#### Using Callbacks
```php
<?php

use RefelctionClass;
use AhjDev\ClassFinder\FindType;
use AhjDev\ClassFinder\ClassFinder;

$finder = new ClassFinder;
$finder->getClasses(
    'AhjDev',
    FindType::TRAIT | FindType::INTERFACE,
    fn (RefelctionClass $v) => $v->isInteranl()
);
```
### Easy methods

There is some easy methods that can use :

```php
<?php
use AhjDev\ClassFinder\FindType;
use AhjDev\ClassFinder\ClassFinder;

$finder = new ClassFinder;
// Whether class is readonly
$finder->isReadonly('foo', FindType::ALL);

// Whether class use this trait
$finder->hasTraitClass('trait', 'foo', FindType::ABSTRACT);

// Whether class implements an interface
$finder->implementsClass('interface', 'foo', FindType::ENUM);

// Whether class is subclass of this class
$finder->isSubClassOf('parent', 'namespace', FindType::FINAL);

// Whether class is of this class or has this class as one of its parents
$finder->isAOf('class', 'namespace', FindType::FINAL);

// Just like `FindType` filters
$finder->isInterface(string $namespace);
$finder->isEnum(string $namespace);
$finder->isTrait(string $namespace);
$finder->isAbstract(string $namespace);
$finder->isFinalClasses(string $namespace);
```
<p align="right">(<a href="#top">back to top</a>)</p>