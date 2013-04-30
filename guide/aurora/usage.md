# General Usage

Once you have correctly set-up your Auroras and Collections, this module
will be able to perform the following operations.

## Main Database API

### Au::load()

Load a Model or a Collection from your database.

Example:

	$person = Au::load('person', 1);

### Au::save()

Save a Model or a Collection to the database.

Example:

	Au::save($person);

### Au::delete()

Delete a Model or a Collection from the database.

Example:

	Au::delete($person);

## Main JSON API

### Au::json_encode()

Encode your Model or your Collection to JSON. This returns a Kohana View.

Example:

	$view = Au::json_encode($person);
	$this->response->body($view->render());

### Au::json_decode()

Decode your Model or your Collection from JSON.

Example:

	$person = Au::json_decode('person', $str_json);
