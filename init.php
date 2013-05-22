<?php

/**
 * Enable the Aurora auto-loader.
 */
spl_autoload_register(array('Aurora_Core', 'auto_load'));

/**
 * Default REST API Route
 */
Route::set(
  'api', array('Aurora_Route', 'route')
);

