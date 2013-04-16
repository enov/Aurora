<?php defined('SYSPATH') or die('No direct access allowed.');

return array
	(
	/**
	 * The default database config for Aurora is PDO
	 * Also **fetch_table_names** is true by default
	 * see the last config variable in this file
	 */
	'default' => array(
		'type'				 => 'pdo',
		'connection'		 => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'		 => 'mysql:host=localhost;dbname=kohana',
			'username'	 => 'root',
			'password'	 => 'r00tdb',
			'persistent' => FALSE,
		),
		/**
		 * The following extra options are available for PDO:
		 *
		 * string   identifier  set the escaping identifier
		 */
		'table_prefix'		 => '',
		'charset'			 => 'utf8',
		'caching'			 => FALSE,
		'profiling'			 => TRUE,
		/**
		 * Prepend the containing table name to each column name returned
		 * in the result set.
		 *
		 * @see PDO::ATTR_FETCH_TABLE_NAMES
		 * @link http://php.net/manual/en/pdo.constants.php PDO::ATTR_FETCH_TABLE_NAMES
		 */
		'fetch_table_names'	 => TRUE,
	),
);