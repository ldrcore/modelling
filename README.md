# Laravel Modelling

A package designed for those who believe that every Model is more than just a bunch of lines.

This package aims to provide basic features to use Models as true relationship entities in data persistance, 
and some other database-like functionatilies that will improve the reusability and the trust of your application. 

## Instalation

Just a simple `composer require ldrcore/modelling` and you can use it. The package use laravel's auto-discover.

## Models

The Models can use our traits for extended functionality. Turn them more than just a query builder!

### Triggable

Creates a lot of hooks that serve as the already-existing database triggers. 
Using those triggers in application level instead of database level allows you more powerful control over code updates.
The triggers are self-explanatory by their own, but in case you don't feel comfortable, just check out the source.

List of allowed triggers:

* beforeCreate()
* afterCreated()
* beforeUpdate()
* afterUpdated($changes = [])
* beforeDelete()
* afterDeleted()
* beforeRestore() ¹
* afterRestored($changes = []) ¹
* beforeForceDelete() ¹
* afterForceDeleted() ¹

¹ only available when using "SoftDeletes"

### Validatable

Creates a set of methods and definitions to you create system-wide validation rules for your models.
For example, a Users table contains the "email" column `not null`. In every single place you can create or update a user's email you should
need to validate this same rule: `'email' => 'required'`. But why? this completely breaks the sense of reusability and allows a lot of mistakes.
Using this trait you define in Model-level this rule, and into every and any operation, this rule will be validated.

### Customizable

Allows the user to customize some uncustomizable features of the Laravel framework regarding the model instance using the package's config file for easy to use.

List of allowed customizations:

* Timestamp names.

With this configuration the timestamp name defined on the package's config file will allow you to use your preferred name without having to add them manually
either on the Blueprint, unfortunately Laravel does not provide an elegant way to override the base Model, so you will still need to use the Trait on your models.

## Connection

This package also provide triggers in connection level. They can be useful to control when an email must really be sent, or a resource must really be deleted from your diskj.
Those triggers can be used from the "DB" facade, and accept a closure as parameters, so you can do mostly anything.

List of allowed triggers:  

* beforeCommit()
* afterCommit()
* beforeRollback()
* afterRollback()

## Persistable

This one is a trait that allows you to quickly add "insert", "update" and "delete" to any class that you wish to control those operations, for example, in your Controller.
By adding this trait to your controller and creating a property `$model` containing the path to the model class, you will have an easy to use collection of methods for persistance.

List of methods:

* insert(array $data)
* update(array $data, $ids) ¹
* updateOrInsert(array $data)
* delete($ids) ¹

¹ the `$ids` can be either a single identifier or a list of identifiers to perform a bulk operation.
