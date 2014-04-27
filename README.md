cocur/watchman
==============

> PHP wrapper for [`facebook/watchman`](https://github.com/facebook/watchman).

[![Latest Stable Version](http://img.shields.io/packagist/v/cocur/watchman.svg)](https://packagist.org/packages/cocur/watchman)
[![Build Status](http://img.shields.io/travis/cocur/watchman.svg)](https://travis-ci.org/cocur/watchman)
[![Code Coverage](http://img.shields.io/coveralls/cocur/watchman.svg)](https://coveralls.io/r/cocur/watchman)


Features
--------

- Simple PHP wrapper for [`facebook/watchman`](https://github.com/facebook/watchman)
- Add, list and delete watched directories
- Add, list and delete triggers
- Compatible with PHP >= 5.4 and [HHVM](http://hhvm.com/)


Installation
------------

You can install `cocur/watchman` through [Composer](https://getcomposer.org):

```shell
$ composer require cocur/watchman:@stable
```

*In a production environment you should replace `@stable` with the [version](https://github.com/cocur/watchman/releases) you want to use.*


Usage
-----

```php
use Cocur\Watchman\Watchman;

$watchman = new Watchman();
$watch = $watchman->addWatch('/var/www/foobar');
$trigger = $watch->addTrigger('foo', '*.js', 'ls -al');

// Retrieve all watched directories
$watched = $watchman->listWatches();

// Retrieve all triggers from a watch
$triggers = $watch->listTriggers();

// Later...
$trigger->delete();
$watch->delete();
```


Supported Watchman commands
---------------------------

- watch ✓
- watch-list ✓
- watch-del ✓
- ~~clock~~
- trigger ✓
- trigger-list ✓
- trigger-del ✓
- ~~find~~
- ~~query~~
- ~~since~~
- ~~log-level~~
- ~~log~~
- shutdown-server ✓
- ~~subscribe~~
- ~~unsubscribe~~
- ~~get-sockname~~


Changelog
---------

### Version 0.1 (27 April 2014)

- Initial release
- Add, delete and list watched directories
- Add, delete and list triggers


Author
------

[**Florian Eckerstorfer**](http://florian.ec)

- [Twitter](http://twitter.com/Florian_)


License
-------

The MIT license applies to **cocur/watchman**. For the full copyright and license information, please view the LICENSE file distributed with this source code.
