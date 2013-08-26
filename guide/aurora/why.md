# Why Aurora

I used to choose the .Net framework (and Java) for projects that were
significantly large. C# language is great, however the underlying framework
always change and break backward compatibility from version to version.

When I came back to PHP, and met the frameworks, I was happy to myself more
productive with PHP, even for larger project. I chose the Kohana framework and
loved the fact that you can easily extend and share codebase via the cascading
file system.

However, I still miss the following:

- Properties (getters and setters): While there was some efforts to have
property accessors included in PHP 5.5, the language still misses them. Aurora
helps you to define getter/setter methods in the form of *get_foo*/*set_foo*;
those methods will be parsed as standard JSON properties, when you use Aurora
built-in JSON encoding APIs.

- Strongly typed collections: the benefits of strongly typed collections are
many. It helps you to easily debug your code. It also helps Aurora to decode
deep JSONs into the specified collection type. Aurora includes an abstract
Collection implementation, which is a bare bone implementation that needs
community review and discussion.

If you are unsure whether or not Aurora is good for you, I have compiled a list
of PROs and CONs to help you decide. YMMV.

## PROs

- Aurora is about freedom. It is Model-first. It gives you the ability to define
domain Models and the choice to map freely your model properties to the database.

- Automatic JSON of Getter/setter properties as standard JavaScript properties.

- Automatic RESTful API to your models, just by extending a single class.

## CONs

- Aurora means more code. If you like database-centric, Auto-modeler ORM, and
the speed it gives you to write an application, then Aurora is NOT for you.

- Loading and JSON encoding of hundreds of thousands deeply nested models,
may not be of good performance, specially in older versions of PHP (5.3).
Customizing JSON encodings (Interface_Aurora_JSON_serialize/deserialize),
using caching techniques might be of good help.