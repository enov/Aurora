# General Usage

Once you have correctly set-up your Auroras and Collections, this module
will be able to perform the following operations.

## Main Database API

### Au::load() or Aurora::factory()->load()

Load a Model or a Collection from your database.

Example:

	$person = Au::load('person', 1);

or

	$person = Aurora::factory('person')->load(1);

### Au::save() or Aurora::factory()->save()

Save a Model or a Collection to the database.

Example:

	Au::save($person);

or

	Aurora::factory('person')->save($person);

### Au::delete() or Aurora::factory()->delete()

Delete a Model or a Collection from the database.

Example:

	$person = Au::load('person', 1);
	Au::delete($person);

or

	Aurora::factory('person')->delete(1);

## Main JSON API

### Au::json_encode()

Encode your Model or your Collection to JSON. This returns a Kohana View.

Example:

	// or
	// $view = Aurora::factory('person')->json_encode($person);
	$view = Au::json_encode($person);
	$this->response->body($view->render());

### Au::json_decode()

Decode your Model or your Collection from JSON.

Example:

	$person = Au::json_decode('person', $str_json);

or

	$person = Aurora::factory('person')->json_decode($str_json);
