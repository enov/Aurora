Aurora
======

A Kohana module to manually map models to database and expose a REST-like interface

![Aurora](aurora.png?raw=true)

Version
--------
Aurora 2.0-alpha for Kohana 3.3, code-named "mighty-math-powers".


Description
------------

Aurora is a Kohana module that helps you

1.  Manually map your models to your database in a separate Aurora class outside
	your models

2.  Encode and decode your models to and from JSON, getters and setters act like
	JavaScript properties without magic.

3.  Work with strongly typed collections of models

4.  Expose a REST-like interface for your resources. This is to serve as an API
	layer to Backbone.js

Installation
------------

Aurora is a Kohana module. It depends on the official Database module.
For a standard installation, copy it into your modules folder and enable it
in your **bootstrap.php** by placing it above the database module:

	/**
	 * Enable modules. Modules are referenced by a relative or absolute path.
	 */
	Kohana::modules(array(
		'aurora'		=> MODPATH.'aurora',		// Aurora module
		'database'		=> MODPATH.'database',		// Database access
		...
	));

TODO
-----

  - Upgrade to Kohana 3.3
  - Set-up new Auth and User modules compatible with Aurora
  - Specify multiple sub-Auroras? multiple tables? multiple pkeys? (post v1)

Documentation:
---------------

Please enable the userguide Kohana module in your **bootstrap.php**.

Nomenclature
---------------

Aurora is named after my wife, **Arshalous**. It is the Armenian word for
"aurora". Literally:
<dl>
  <dt>Արշալոյս</dt>
  <dd>twilight of the dawn</dd>
</dl>

License
--------

##### The MIT License (MIT)

###### Copyright © 2013 Samuel Demirdjian

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

