# 🔀 UXDM Eloquent

[![Build Status](https://travis-ci.com/DivineOmega/uxdm-eloquent.svg?branch=master)](https://travis-ci.com/DivineOmega/uxdm-eloquent)
[![Coverage Status](https://coveralls.io/repos/github/DivineOmega/uxdm-eloquent/badge.svg?branch=master)](https://coveralls.io/github/DivineOmega/uxdm-eloquent?branch=master)

The UXDM Eloquent package provides a UXDM source and destination for the
Eloquent ORM, commonly used in Laravel projects.

## Installation

To install the UXDM Eloquent package, just run the following composer 
command.

```bash
composer require divineomega/uxdm-eloquent
```

## UXDM Eloquent Source

The UXDM Eloquent source allows you to source data from an Eloquent model. This can be handy if you need to migrate data
from a system using the Eloquent ORM, such as a Laravel project.

### Creating

To create a new Eloquent source, you must provide it with the class name of Eloquent model you wish to use.

The following example creates a Eloquent source object, using an Eloquent model called `User` in the `App` namespace.

```php
$eloquentSource = new EloquentSource(\App\User::class);
```

You can also pass a query callback as a second parameter to restrict the results returned, as shown below.

```php
$eloquentSource = new EloquentSource(\App\User::class, function($query) {
    $query->where('id', 1);
});
```

### Assigning to migrator

To use the Eloquent source as part of a UXDM migration, you must assign it to the migrator. This process is the same for most sources.

```php
$migrator = new Migrator;
$migrator->setSource($eloquentSource);
```


## UXDM Eloquent Destination

The UXDM Eloquent destination allows you to migrate data into an Eloquent model. This can be handy if you need to migrate data
into a system using the Eloquent ORM, such as a Laravel project.

### Creating

To create a new Eloquent destination, you must provide it with the class name of Eloquent model you wish to use.

The following example creates a Eloquent destination object, using an Eloquent model called `User` in the `App` namespace.

```php
$eloquentDestination = new EloquentDestination(\App\User::class);
```

### Assigning to migrator

To use the Eloquent destination as part of a UXDM migration, you must assign it to the migrator. This process is the same for most destinations.

```php
$migrator = new Migrator;
$migrator->setDestination($eloquentDestination);
```

Alternatively, you can add multiple destinations, as shown below. You can also specify the fields you wish to send to each destination by 
passing an array of field names as the second parameter.

```php
$migrator = new Migrator;
$migrator->addDestination($eloquentDestination, ['field1', 'field2']);
$migrator->addDestination($otherDestination, ['field3', 'field2']);
```
