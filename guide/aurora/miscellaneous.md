# Miscellaneous

## Transactions

All database operations are transactional. You can disable this functionality
in your `Aurora_` classes by setting the `transactional` property to false.

For example:

	class Aurora_Event extends Interface_Aurora_Database, ...
	{

		...

		public $transactional = FALSE;

		...

	}

## Profiling

Aurora adds Profiler Marks for you to benchmark your application, detailed for
each method of the Core API, all under the single "aurora" category.

You **JUST** need to enable Kohana profiling (which is enabled by default)

You may read the official manual about profiling [here](../kohana/profiling).