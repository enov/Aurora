# Expose Your RESTful API

Aurora helps you to expose your Models to a RESTful API. This way you can CRUD
your Models directly from Backbone.js.

## Steps to enable a RESTful API

In order to have automatic routing to your RESTful API:

1. In your bootstrap, Set this route with a filter to the Aurora_Route::map

        // Aurora RESTful API
        Route::set('RESTful-api', 'api/<path>', array('path' => '.*'))
          ->filter(array('Aurora_Route', 'map'));

2. Either create a controller or make use of config based routing:

  2.1. Name your API controllers by starting with "Controller_API_"
  and Extend them from Controller_API

  2.2. Add a config file routes.php in your `config` folder

            return array(
                'api' => array(
                    'Calendar_Category',
                    'Calendar_Event',
                ),
            );



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

[!!] You may want to place the default API route as your last route,
or just before your default catch-all route
