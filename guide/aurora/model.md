# Creating Your Models

Your models can be anything, and extend any class, provided that they are named "Model_".

Constraint: ID autonumber

### A model example

Your models can be as simple as this one...

    class Model_User
    {
        public $id;
        public $username;
        public $firstname;
        public $lastname;
    }

### Another model example

... or as verbose as the one below.

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

Whatever your way of modeling, Aurora can manage your models.

### Type hinting and Lazy Loading

You have probably guessed that Aurora encourages the verbose version, but also
works fine and respects the developers who use public properties and then do the
validation and data filtering when saving to the database.

[!!] IMHO, having getters and setters helps me write business logic around my
Model properties. Programming aroung properties means building a RESTful app,
because setting property values runs business code.

Moreover, Aurora respects:

- the use of **type hints** in setters
- **lazy loading** uninitialized properties in getters.

Please consider the example below, for type hinting and lazy loading.

	class Model_User
	{

		...

		/**
		 * Collection_User_Role of the user
		 */
		protected $_roles;
		public function get_roles() {
			if ($this->_roles == NULL)
				$this->_roles = Collection::factory('User_Role');
			return $this->_roles;
		}
		protected function set_roles(Collection_User_Role $roles) {
			$this->_roles = $roles;
			return $this;
		}

		...

	}

While converting deep JSON objects, type hinting will help Aurora to know the
Models and/or the Collections to convert JSON to. This is done by using Reflection,
Aurora will try to read the type of the **type hinted** setter and will apply
JSON decoding accordingly.