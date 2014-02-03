<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Manual data mapping interface. Those methods are called
 * on database read and write. "Aurora_" classes should
 * implement this.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Database extends Interface_Aurora_Database_Persist,
 Interface_Aurora_Database_Retrieve
{

}
