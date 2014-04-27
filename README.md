cocur/watchman
==============

> PHP wrapper for [`facebook/watchman`](https://github.com/facebook/watchman).


Features
--------


Installation
------------


Usage
-----

```php
use Cocur\Watchman\Watchman;

$watchman = new Watchman();
$watchman->watch('/var/www/foobar');
$watchman->trigger('/var/www/foobar', 'foo', '*.js', 'ls -al');
```


Supported Watchman commands
---------------------------

- [x] watch
- [ ] watch-list
- [ ] watch-del
- [ ] clock
- [x] trigger
- [ ] trigger-list
- [ ] trigger-del
- [ ] find
- [ ] query
- [ ] since
- [ ] log-level
- [ ] log
- [ ] shutdown-server
- [ ] subscribe
- [ ] unsubscribe
- [ ] get-sockname
- [ ]


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
