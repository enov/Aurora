# Filter, sort and limit your data

## Filtering out data

Filtering of data can be directly done by passing the `$param` parameter to the
core API `load($object, $params = NULL)` method.

Also, Aurora relies on the `$qview` property of your Aurora files when loading.

Therefore, you can manipulate the `$qview` property, within your Aurora files
by creating methods.

Example:

	class Aurora_Event implements Interface_Aurora_Database {

		/** @property Database_Query_Builder_Select $qview */
		public $qview;

		public function qview() {
			return DB::select()->from('events')
				->join('categories')->on('events.cat_id', '=', 'categories.id')
		}

		public function filter_by_category(Model_Category $category) {

			// test if $role parameter has an ID loaded from database
			if (Au::is_new($category))
				throw new Kohana_Exception('Please provide a loaded category');

			// test if qview property is initiated
			// if not initiate it with method qview()
			$this->qview = $this->qview ? : $this->qview();

			// set the qview property for this Aurora with filtering
			$this->qview->where('events.cat_id', '=', $category->get_id());

		}

		public function db_persist($model) { ... }

		public function db_retrieve($model, array $row) { ... }

	}

Later, in your controller you can do:

	$category = Au::load('category', $cat_id);

	$filtered_events = Aurora::factory('event')
		->filter_by_category($category)
		->load();

## Sorting out data

Example:

	class Aurora_Event implements Interface_Aurora_Database {

		/** @property Database_Query_Builder_Select $qview */
		public $qview;

		public function qview() {
			return DB::select()->from('events')
				->join('categories')->on('events.cat_id', '=', 'categories.id')
		}

		public function sort_by_category() {

			// test if qview property is initiated
			// if not initiate it with method qview()
			$this->qview = $this->qview ? : $this->qview();

			// set the qview property for this Aurora with sorting
			$this->qview->order_by('events.cat_id', 'ASC');

		}

		public function db_persist($model) { ... }

		public function db_retrieve($model, array $row) { ... }

	}

Later, in your controller you can do:

	$sorted_events = Aurora::factory('event')
		->sort_by_category()
		->load();

## Limiting data

Example:

	class Aurora_Event implements Interface_Aurora_Database {

		/** @property Database_Query_Builder_Select $qview */
		public $qview;

		public function qview() {
			return DB::select()->from('events')
				->join('categories')->on('events.cat_id', '=', 'categories.id')
		}

		public function limit($num) {

			// test if $num parameter has an ID loaded from database
			if (Valid::digit($num))
				throw new Kohana_Exception('Please provide a valid number');

			// test if qview property is initiated
			// if not initiate it with method qview()
			$this->qview = $this->qview ? : $this->qview();

			// set the qview property for this Aurora with sorting
			$this->qview->limit($num);

		}

		public function db_persist($model) { ... }

		public function db_retrieve($model, array $row) { ... }

	}

Later, in your controller you can do:

	$hundred_events = Aurora::factory('event')
		->limit(100)
		->load();