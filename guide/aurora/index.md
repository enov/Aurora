# Aurora

Aurora is a Kohana module for manual ORM, JSON, and REST.

## Introduction

Aurora helps you load and save your models from/to the database. It is a library
for **manual** ORM. Aurora is **NOT an Auto-Modeler**. Which means that probably
it's not an ORM, after all...

Aurora expects that you map database columns and object properties by hand. That
means you have to write more code. However, those "mappings" are organized into
a separate "Aurora_" files, thus helping you to remove all database logic from
your Models. Your models become just business models.

For those of you who like accessors methods over public properties, and do
"property programming", Aurora is here to respect those methods. While encoding
your models to JSON, Aurora converts your getters and setters into standard
properties and exposes a RESTlike interface.

Backbone.js is a first class citizen at Aurora. The "RESTful" API of Aurora is
primarily intended to be consumed by Backbone.js.

## Nomenclature

Aurora is named after my wife, **Arshalous**. It is the Armenian word for
"aurora", (literally *twilight of the dawn*).