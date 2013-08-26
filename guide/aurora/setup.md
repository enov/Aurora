# Setup

A standard Aurora setup for each model should include.

1. The [Model](setup_model) class. Naming should start with "Model_"

2. The [Collection](setup_collection) class. Naming should start with "Collection_". The class
should extend the abstract Collection provided with the module.

3. The [Aurora](setup_aurora) class specific to this model. Naming should start with "Aurora_".
The class should at least implement the Interface_Aurora_Database.

[!!] By default, the Aurora auto-loader is enabled. This is why, Collection and
Aurora classes can be defined in the Model class file. By convention, put the
Aurora class under the Model class, and the Collection class under Aurora.