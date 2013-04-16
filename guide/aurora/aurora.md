# Creating Your Aurora_'s

You need to define an Aurora_ class for each of your Models. All database logic
should be defined in this class.

Constraint: ID autonumber

### A model example

    class Model_User
    {
        public $id;
        public $username;
        public $firstname;
        public $lastname;
    }

### Another model example

	class Model_User
	{

		/**
		 * ID of the user
		 */
		protected $_id;
		public function get_id() {
			return $this->_id;
		}
		protected function set_id($id) {
			if (!Valid::id($id))
				throw new Kohana_Exception("Invalid News Category ID");
			$this->_id = (int) $id;
			return $this;
		}

		/**
		 * username for the user
		 */
		protected $_username;
		public function get_username() {
			return $this->_username;
		}
		public function set_username($username) {
			// some validation function I use for usernames in my projects
			if (!Valid::username($id))
				throw new Kohana_Exception("Invalid username specified");
			$this->_username = $username;
			return $this;
		}

		/**
		 * firstname for the user
		 */
		protected $_firstname;
		public function get_firstname() {
			return $this->_firstname;
		}
		public function set_firstname($firstname) {
			if (!Valid::not_empty($firstname))
				throw new Kohana_Exception("Please specify a value for firstname");
			if (!Valid::max_length($firstname, 50))
				throw new Kohana_Exception("Please enter a valid firstname");
			$this->_firstname = $firstname;
			return $this;
		}

		/**
		 * lastname for the user
		 */
		protected $_lastname;
		public function get_lastname() {
			return $this->_lastname;
		}
		public function set_lastname($lastname) {
			if (!Valid::not_empty($lastname))
				throw new Kohana_Exception("Please specify a value for lastname");
			if (!Valid::max_length($lastname, 50))
				throw new Kohana_Exception("Please enter a valid lastname");
			$this->_lastname = $lastname;
			return $this;
		}

		/**
		 * Calculated, read-only fullname for the user
		 */
		public function get_fullname() {
			return ucfirst($this->_firstname) . ' ' . ucfirst($this->_lastname);
		}
	}
