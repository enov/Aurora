<?php

/**
 * Enable the Aurora auto-loader.
 */
spl_autoload_register(array('Aurora_Core', 'auto_load'));

/**
 * Default REST API Route
 */

// In Kohana 3.2 enable the API route in your bootstrap.php, as follows:
// Route::set('rest-api', array('Aurora_Route', 'route'));

// Aurora does not officially support Kohana 3.3, but you may want to try this:
//Route::set('rest-api', 'api')
//    ->filter(array('Aurora_Route', 'route'));

