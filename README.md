Aurora
======

A kohana module to manually map models to database and expose a REST-like interface

![Aurora](aurora.png?raw=true)

Version
--------
Aurora 0.4-alpha for Kohana 3.2


Description
------------
Aurora is a Kohana module that helps you

1.  Manually map your models to your database in a separate file outside your models

2.  Encode and decode your models to and from JSON, getters and setters act like
	properties without magic.

3.  Work with strongly typed collections of models

4.  Expose a REST-like interface for your resources. This is to serve as an API layer
	to Backbone.js

Installation
------------

Aurora is a Kohana module. Copy Aurora into your modules folder and enable it
in your bootstrap.php:

	/**
	 * Enable modules. Modules are referenced by a relative or absolute path.
	 */
	Kohana::modules(array(
		'aurora'		=> MODPATH.'aurora',		// Aurora module
		...
	));


TODO
-----
  - Upgrade to Kohana 3.3
  - Add some tests
  - Add profiling points
  - Specify multiple database tables in Aurora
  - Specify multiple primary keys for each table in Aurora
  - Merge krestful into the project
  - Set-up new Auth and User modules compatible with Aurora
  - Take care of PSR-2 getters/setters? (low-priority?)

Documentation:
---------------
Please refer to the guide folder, or enable the userguide Kohana module.


License
--------
MIT
