# Creating Your Models

There is nothing fancy here. Your models can be anything, and extend any class, provided that they are named "Model_".

Constraint: ID autonumber

### A model example

    class Model_Event
    {
        public $id;
        public $title;
        public $start_date;
        public $end_date;
    }

### Another model example

    class Model_Event
    {
	    /**
	     * ID of then News Item
	     */
		private $_id;
		public function get_id() {
			return $this->_id;
		}
		protected function set_id($id) {
			if (!Valid::id($id))
				throw new Kohana_Exception("Invalid News Category ID");
			$this->_id = (int) $id;
		}

        public $title;
        public $start_date;
        public $end_date;
    }
