# Creating Your Auroras

You need to define an Aurora class for each of your Models. All database logic
should be defined in this class.

You can extend your Aurora class with your Model, so that you can reach the
protected properties and methods. Note that this is optional.

However, you are required to implement the Aurora_Interface:

    class Aurora_Person extends Model_Person implements Aurora_Interface
    {
		public static function db_persist($model){
			return array(
				'id' => $model->id,
				'name' => $model->name,
			);
		}
		public static function db_retrieve($model, array $row){
			$model->id = $row['persons.id'];
			$model->name = $row['persons.name'];
		}

    }

## Optional Setup elements

### Config (optional)

The $config property allows you to choose the database config group you
want the Aurora to run the queries upon.

Defaults to: default

[!!] Aurora has a hacked version of Kohana_Database_PDO where it allows
to have "namespaced" / or "fully-qualified" column names, that is
table names along the column names, separated with a dot. You can see
an example of this above, in the example **db_retrieve** function. You
need to add a configuration flag in the config group of the database.
An example of a database config group is available with the module.

Example:

    public static $config = 'pdo';

### Table (optional)

The $table property allows you to specify the database table upon which
your model is based upon.

Defaults to: strtolower the common name and adds an 's' at the end. Like
**persons** for **Model_Person**

Example:

    public static $table = 'categories';

### Primary Key (optional)

The $pkey property allows you to specify the database table primary key.

Defaults to: id

Example:

    public static $pkey = 'guid';


### Query View (optional)

The qview() method specifies the query you want to run in order to populate
your Models properties.

[!!] Aurora's Query View (or qview) has nothing to do with the MVC view. It
is rather a hard-coded Database_Query representing a database view as in say
**MySQL view**

You can have a complex query view with multiple table joined together. As
column names can be separated by table names, you can easily populate your
Models property in **db_retrieve**.

Defaults to: DB::select()->from(static::$table)

Example:

    public static function qview() {
		return DB::select()->from('persons')
					->join('users')->on('persons.user_id', '=', 'users.id')
    }



