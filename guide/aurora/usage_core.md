# General Usage

Once you have correctly setup your Auroras and Collections, you can use the API
below, from your controllers (in general).

[!!] The **Au** class is a shortcut to access the core API. Otherwise, you can
decorate your *Aurora_* class with the core methods, using **Aurora::factory**.

The core API methods are devided into 2 sets

1. The database API: loading/saving/deleting models.

2. The JSON API: JSON encoding and decoding.

## Core Database API

### Loading

#### Au::load() or Aurora::factory()->load()

Load a Model or a Collection from your database.

Example:

	$person = Au::load('person', 1);

or

	$person = Aurora::factory('person')->load(1);

### Saving

#### Au::save() or Aurora::factory()->save()

Save a Model or a Collection to the database.

Example:

	Au::save($person);

or

	Aurora::factory('person')->save($person);

### Deleting

#### Au::delete() or Aurora::factory()->delete()

Delete a Model or a Collection from the database.

Example:

	$person = Au::load('person', 1);
	Au::delete($person);

or

	Aurora::factory('person')->delete(1);

## Core JSON API

### Encoding

#### Au::json_encode() or Aurora::factory()->json_encode()

Encode your Model or your Collection to JSON. This returns a Kohana View.

Example:

	// or
	// $view = Aurora::factory('person')->json_encode($person);
	$view = Au::json_encode($person);
	$this->response->body($view->render());

### Decoding

#### Au::json_decode() or Aurora::factory()->json_decode()

Decode your Model or your Collection from JSON.

Example:

	$person = Au::json_decode('person', $str_json);

or

	$person = Aurora::factory('person')->json_decode($str_json);
