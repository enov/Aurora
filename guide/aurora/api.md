# Expose Your API

Aurora helps you to expose a REST-like API by extending the Controller_API.

## Optional Automatic routing

### Steps to enable automatic routing

In order to have automatic routing to your API controllers:

1. Name your API controllers by starting with "Controller_API_"

2. Extend them from Controller_API

3. Enable the Aurora_Route::route built-in route in your bootstrap

You may want to place it as your first route, as it will first try to match
routes that start with 'api/'.


	Route::set('api', array('Aurora_Route', 'route'));



### Example

	class Controller_API_Calendar_Event extends Controller_API
	{
	}

In this example, your Model_Calendar_Event and Collection_Calendar_Event are
automatically published via REST.

	// will return JSON representation of Model_Calendar_Event with id 1
	**GET** /api/calendar/event/1

	// will return JSON representation of Collection_Calendar_Event
	**GET** /api/calendar/event

	// will create a Model_Calendar_Event on server
	**POST** /api/calendar/event

	// will update an existing Model_Calendar_Event object on server
	**PUT** /api/calendar/event

	// will delete an existing Model_Calendar_Event with id 1
	**DELETE** /api/calendar/event/1

