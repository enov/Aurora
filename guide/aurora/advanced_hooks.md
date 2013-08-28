# Hooks

Aurora provides a set of 10 hooks for you to have a fine-grained control when
loading, saving and deleting models.

The hooks cover the database API methods - `load()`, `save()`, `delete()` - plus
`create()` and `update()` protected database methods (not available in the core API).

For each of these methods, we have 2 hooks: the **before** and the **after** hooks.

Please look at the table below to have a general view of all the available hooks.

Main API        | Interface                           | Method
----------------|-------------------------------------|--------------------------------------
Loading         | Interface_Aurora_Hook_Before_Load   | `before_load(&$params)`
Loading         | Interface_Aurora_Hook_After_Load    | `after_load($model_or_collection)`
Saving          | Interface_Aurora_Hook_Before_Save   | `before_save($model_or_collection)`
Saving          | Interface_Aurora_Hook_After_Save    | `after_save($model_or_collection)`
Saving (create) | Interface_Aurora_Hook_Before_Create | `before_create($model)`
Saving (create) | Interface_Aurora_Hook_After_Create  | `after_create($model)`
Saving (update) | Interface_Aurora_Hook_Before_Update | `before_update($model)`
Saving (update) | Interface_Aurora_Hook_After_Update  | `after_update($model)`
Deleting        | Interface_Aurora_Hook_Before_Delete | `before_delete($model_or_collection)`
Deleting        | Interface_Aurora_Hook_After_Delete  | `after_delete($model_or_collection)`

## Before | After Loading

[!!] For users of PHP version < 5.3.10, it seems that there is a bug
in *call_user_func_array*. The function is used in Aurora_Hook.
It does not respect **by reference** calls. This mainly affects the *before_load*
hook.

### Before Load: `Interface_Aurora_Hook_Before_Load`

    public function before_load(&$params);

The purpose of this hook is to modify the `$params` argument that are passed to the
core API `load($object, $params = NULL)` method.

Your Aurora needs to implement the `Interface_Aurora_Hook_Before_Load`.

**Example**: a hook to make Aurora load a user by username and email

    class Aurora_User implements Interface_Aurora_Hook_Before_Load, ...
    {

        public function before_load(&$params) {

            // if parameter is an email, load the user by email
			if (Valid::email($params))
                $params = array('email' => $params);

            // if parameter is a username, load the user by username
            else if (Valid::username($params))
                $params = array('username' => $params);

        }

        ...

    }

Later on, you can load your Model_User from your controller:

    $user = Au::load('user', 1); // load user by ID

or

    $user = Au::load('user', 'user@example.com'); // load user by email

or

    $user = Au::load('user', 'enov'); // load user by username 'enov'

### After Load: `Interface_Aurora_Hook_After_Load`

    public function after_load($model_or_collection);

The purpose of this hook is to give you the control to make additional changes to
the model or collection you will receive, after it gets loaded from the database.

Probably, you may want to load many-to-many relationships of your models, or sort
the loaded collection, or read something from a file... There may be many needs.

[!!] This hook will run only **once**, at the end of the core API
`load($object, $params = NULL)` method. For performance reasons, it will **NOT**
run separately for each model within the collection, rather will pass the collection
in full as a parameter to your implementation.

If you implement this method, you will receive either a model or a collection,
whatever it was `load()`ed. You may want to test the type of the parameter you
will receive:

    class Aurora_User implements Interface_Aurora_Hook_After_Load, ...
    {

        public function after_load($model_or_collection) {

            // if parameter is a model
			if (Au::type()->is_model($model_or_collection))

                ...

            // if parameter is a collection
            else if (Au::type()->is_collection($model_or_collection))

                ...

        }

        ...

    }

## Before | After Saving

### Before Save: `Interface_Aurora_Hook_Before_Save`

    public function before_save($model_or_collection);

The purpose of this hook is to give you the control to make additional changes to
the model or the collection you will receive, or you want to validate or something from
the database, before the model or the collection you receive actually gets saved.

[!!] This hook will run only **once**, at the beginning of the core API
`save($object)` method. For performance reasons, it will **NOT**
run separately for each model within the collection, rather will pass the collection
in full as a parameter to your implementation.

### After Save: `Interface_Aurora_Hook_After_Save`

    public function after_save($model_or_collection);

The purpose of this hook is to give you the control to make additional operations
after the model or the collection of models is saved. For example, you might want
to add a row in another table, log the changes, or create a user folder in the file system.

[!!] This hook will run only **once**, at the end of the core API
`save($object)` method. For performance reasons, it will **NOT**
run separately for each model within the collection, rather will pass the collection
in full as a parameter to your implementation.

## Before | After Creating

### Before Create: `Interface_Aurora_Hook_Before_Create`

    public function before_create($model);

The purpose of this hook is to give you control specifically before a model is
created. You may want to ensure if a username is available, or validate something
in the database, or create a row in another table and have its ID before saving
this model.

[!!] This hook will run for each and every model in the collection you pass in
the `save($object)` core API method. Please use this hook wisely as it may
affect the performance of your application.

### After Create: `Interface_Aurora_Hook_After_Create`

    public function after_create($model);

The purpose of this hook is to give you control specifically after a model is
created. You may want to create a folder in the file system, or add a row in the
database using the newly created model's ID, etc...

[!!] This hook will run for each and every model in the collection you pass in
the `save($object)` core API method. Please use this hook wisely as it may
affect the performance of your application.

## Before | After Updating

### Before Update: `Interface_Aurora_Hook_Before_Update`

    public function before_update($model);

The purpose of this hook is to give you control specifically before a model is
updated.

[!!] This hook will run for each and every model in the collection you pass in
the `save($object)` core API method. Please use this hook wisely as it may
affect the performance of your application.

### After Update: `Interface_Aurora_Hook_After_Update`

    public function after_update($model);

The purpose of this hook is to give you control specifically after a model is
updated.

[!!] This hook will run for each and every model in the collection you pass in
the `save($object)` core API method. Please use this hook wisely as it may
affect the performance of your application.

## Before | After Deleting

### Before Delete: `Interface_Aurora_Hook_Before_Delete`

    public function before_delete($model_or_collection);

The purpose of this hook is to give you the control to make additional changes to
the model or the collection, you will receive for deletion.

[!!] This hook will run only **once**, at the beginning of the core API
`delete($object)` method. For performance reasons, it will **NOT**
run separately for each model within the collection, rather will pass the collection
in full as a parameter to your implementation.

### After Delete: `Interface_Aurora_Hook_After_Delete`

    public function after_delete($model_or_collection);

The purpose of this hook is to give you the control to make additional changes to
the model or the collection that is deleted.

[!!] This hook will run only **once**, at the end of the core API
`delete($object)` method. For performance reasons, it will **NOT**
run separately for each model within the collection, rather will pass the collection
in full as a parameter to your implementation.

