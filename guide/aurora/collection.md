# Creating Your Collections

A "Collection" in Aurora, is a kind of PHP array wrapper to enforce the type of
the objects within the collection.

A Collection can be easily defined by extending the Aurora_Collection class.

The Aurora_Collection class decorates the PHP array with methods that enforce
type checking.

### A collection example

	// class to act like array but only accepts objects of type Model_User
    class Collection_User extends Aurora_Collection
    {
    }

Once you have defined the above Collection_User, you have defined a strictly
typed Collection to accept objects of type Model_User.