Aurora
======

A kohana module to manually map models to database and expose a REST-like interface

![Aurora](aurora.png?raw=true)

Version
--------
Aurora 0.5-beta for Kohana 3.2


Description
------------

Aurora is a Kohana module that helps you

1.  Manually map your models to your database in a separate class outside your models

2.  Encode and decode your models to and from JSON, getters and setters act like
	properties without magic.

3.  Work with strongly typed collections of models

4.  Expose a REST-like interface for your resources. This is to serve as an API layer
	to Backbone.js

Installation
------------

Aurora is a Kohana module. It depends on the official Database module.
For a standard installation, copy Aurora into your modules folder and enable it
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
  - Add some tests and a test application
  - Specify multiple sub-Auroras? multiple tables? multiple pkeys?  (post v1)
  - Set-up new Auth and User modules compatible with Aurora
  - Sync documentation, add examples

Documentation:
---------------
Please refer to the guide folder, or enable the userguide Kohana module.

Unfortunately, docs are not in sync with the latest release.


License
--------

MIT
