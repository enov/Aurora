# Creating Your Auroras

You need to define an Aurora class for each of your Models. All database logic
should be defined in this class.

You can extend your Aurora class with your Model, so that you can reach the
protected properties and methods. Note that this is optional.

However, you are required to implement the Aurora_Interface:

    class Aurora_Person extends Model_Person implements Aurora_Interface
    {
		public static function db_from_model($model){
			return array(
				'id' => $model->id,
				'name' => $model->name,
			);
		}
		public static function db_to_model($model, array $row){
			$model->id = $row['persons.id'];
			$model->name = $row['persons.name'];
		}

    }