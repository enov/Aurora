<?php

/**
 * Overrides the connect function of the default Database_PDO
 * class to support fetching the table names in the result sets.
 *
 * example:
 *
 *     $rowset = DB::select()->from('mytable')
 *                 ->join('mycategory')
 *                 ->on('mytable.cat_id', '=', 'mycategory.id')
 *                 ->execute('pdo');
 *
 *     foreach ($rowset as $row) {
 *         $id = $row['mytable.id']; // instead of $row['id']
 *         $cat_name = $row['mycategory.name'];
 *     }
 *
 * This helps you query the database once and load related, nested Models.
 *
 * @package Aurora
 * @category Database
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 * @see PDO::ATTR_FETCH_TABLE_NAMES
 * @link http://php.net/manual/en/pdo.constants.php PDO::ATTR_FETCH_TABLE_NAMES
 */
class Aurora_Database_PDO extends Kohana_Database_PDO
{

	public function connect() {
		if ($this->_connection)
			return;

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'dsn' => '',
			'username' => NULL,
			'password' => NULL,
			'persistent' => FALSE,
			'fetch_table_names' => FALSE,
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		// Force PDO to use exceptions for all errors
		$attrs = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

		// Force PDO to disable server-side prepared statements
		$attrs[PDO::ATTR_EMULATE_PREPARES] = FALSE;

		// Force PDO not to stringify fetches
		$attrs[PDO::ATTR_STRINGIFY_FETCHES] = FALSE;

		if (!empty($persistent)) {
			// Make the connection persistent
			$attrs[PDO::ATTR_PERSISTENT] = TRUE;
		}

		if (!empty($fetch_table_names)) {
			// fetch table names with the columns names
			$attrs[PDO::ATTR_FETCH_TABLE_NAMES] = TRUE;
		}

		try {
			// Create a new PDO connection
			$this->_connection = new PDO($dsn, $username, $password, $attrs);
		} catch (PDOException $e) {
			throw new Database_Exception(':error', array(':error' => $e->getMessage()), $e->getCode());
		}

		if (!empty($this->_config['charset'])) {
			// Set the character set
			$this->set_charset($this->_config['charset']);
		}
	}

}
