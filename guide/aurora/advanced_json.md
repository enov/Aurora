# JSON encoding/decoding

By default, Aurora reflects upon your models and serializes them to JSON. Public
properties on your Models, as well as public getters (and setters, in case of
de-serialization) will become standard JavaScript properties.

For example, consider this Model:

	class Model_Category {

		public $id;

		protected $_title;
		public function get_title() {
			return $_title;
		}
		public runction set_title($title) {
			$this->_title = $title;
		}

	}

JSON encoding this Model will result in:

	{
		id: 1,
		title: 'my title'
	}

## Custom JSON encoding

Aurora allows you to override the default JSON serialization, by implementing
Interface_Aurora_JSON_Serialize and Interface_Aurora_JSON_Deserialize interfaces.

You can implement either, or both of the interfaces.

By implementing those interface properly, your application will have a performance
boost as Aurora will bypass the default Model reflection algorithms and use your
implemented functions.

for example:

	class Aurora_Category implements Interface_Aurora_JSON_Serialize,
	Interface_Aurora_JSON_Deserialize, ...
	{
		...

		public function json_serialize($model) {
			$std = new stdClass;
			$std->id = $model->id;
			$std->title = $model->get_title();
			return $std;
		}

		public function json_deserialize($json) {
			$m = new Model_Category;
			$m->id = $json->id;
			$m->set_title($json->title);
			return $m;
		}
	}