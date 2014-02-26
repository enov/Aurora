<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON serialization and deserialization.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Database extends Interface_Aurora_JSON_Serialize,
 Interface_Aurora_JSON_Deserialize
{

}

