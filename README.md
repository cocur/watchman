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

<input type="checkbox" checked> watch
<input type="checkbox"> watch-list
<input type="checkbox"> watch-del
<input type="checkbox"> clock
<input type="checkbox" checked> trigger
<input type="checkbox"> trigger-list
<input type="checkbox"> trigger-del
<input type="checkbox"> find
<input type="checkbox"> query
<input type="checkbox"> since
<input type="checkbox"> log-level
<input type="checkbox"> log
<input type="checkbox"> shutdown-server
<input type="checkbox"> subscribe
<input type="checkbox"> unsubscribe
<input type="checkbox"> get-sockname
<input type="checkbox">


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
