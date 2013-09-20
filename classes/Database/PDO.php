<?php

/**
 * Overrides the connect function of the default Database_PDO
 * class to support fetching the table names in the result sets.
 *
 * @see PDO::ATTR_FETCH_TABLE_NAMES
 * @link http://php.net/manual/en/pdo.constants.php PDO::ATTR_FETCH_TABLE_NAMES
 */
class Database_PDO extends Aurora_Database_PDO
{
	
}