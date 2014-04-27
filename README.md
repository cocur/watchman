cocur/watchman
==============

> PHP wrapper for [`facebook/watchman`](https://github.com/facebook/watchman).

[![Build Status](http://img.shields.io/travis/cocur/watchman.svg)](https://travis-ci.org/cocur/watchman)
[![Code Coverage](http://img.shields.io/coveralls/cocur/watchman.svg)](https://coveralls.io/r/cocur/watchman)


Features
--------


Installation
------------


Usage
-----

```php
use Cocur\Watchman\Watchman;

$watchman = new Watchman();
$watch = $watchman->watch('/var/www/foobar');
$watch->addTrigger('foo', '*.js', 'ls -al');
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

*Currently there exists no release.*


Author
------

[**Florian Eckerstorfer**](http://florian.ec)

- [Twitter](http://twitter.com/Florian_)


License
-------

The MIT license applies to **cocur/watchman**. For the full copyright and license information, please view the LICENSE file distributed with this source code.
