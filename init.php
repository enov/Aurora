<?php

Route::set(
  'aurora_api', function($uri) {
	  $pieces = explode('/', $uri);
	  // if $uri does not start with api/
	  // it has nothing to do with this route
	  $api = array_shift($pieces);
	  if ($api != 'api')
		  return false;
	  // Get the last piece of the uri
	  // and test if it contains an ID
	  $last = array_pop($pieces);
	  if (Valid::digit($last)) {
		  $id = $last;
		  $controller = array_pop($pieces);
	  } else {
		  $id = NULL;
		  $controller = $last;
	  }
	  // construct $directory and common_name
	  $directory = $api . ($pieces ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces) : '' );
	  $common_name = ($pieces ? implode('_', $pieces) . '_' : '') . $controller;
	  // See if exists a Controller of the form
	  // Controller_API_Common_Name
	  if (Kohana::find_file('classes/controller', $directory . DIRECTORY_SEPARATOR . $controller)) {
		  return array(
			  'directory' => $directory,
			  'controller' => $controller,
			  'common_name' => $common_name,
			  'id' => $id,
		  );
	  } else {
		  // Test if common_name is registered in the config
		  // to use the default Controller_API
		  if (in_array($common_name, Kohana::$config->load('routes.api'))) {
			  return array(
				  'directory' => NULL,
				  'controller' => 'api',
				  'common_name' => $common_name,
				  'id' => $id,
			  );
		  } else {
			  // no luck, return false
			  return false;
		  }
	  }
  });