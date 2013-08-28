#Advanced

This section gives you an idea about how you can filter out your data and how
you can have a fine-grained control over the default implementation of Aurora's main API:

1. [Filter, order and limit](advanced_filter) class. Naming should start with "Model_"

2. The [Collection](advanced_hooks) class. Naming should start with "Collection_". The class
should extend the abstract Collection provided with the module.

3. [Custom JSON encoding/decoding](advanced_json) to override the default implementation
and enhance the performance of your application.
