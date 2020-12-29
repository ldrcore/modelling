# Laravel Modelling

A package designed for those who believe that every Model is more than just a bunch of lines.

This package aims to provide basic features to use Models as true relationship entities in data persistance, 
and some other database-like functionatilies that will improve the reusability and the trust of your application. 

All of this with all base classes (Connection, Builders) being able to be extended and easily defined in the package's configuration file.

## Instalation

Just a simple `composer require ldrcore/modelling` and you can use it. The package use laravel's auto-discover.

## Models

The Models can use our traits for extended functionality. Turn them more than just a query builder!

### Triggable

Creates a lot of hooks that serve as the already-existing database triggers. 
Using those triggers in application level instead of database level allows you more powerful control over code updates.
The triggers are self-explanatory by their own, but in case you don't feel comfortable, just check out the source.
This triggers can be created within the Model or an Observer, when using in the Observer you *MUST* extend the `TriggableObserver` class.

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

### MassTriggable

When you need to deal with large amount of data and still desire to use the Triggable usefullness.   
This trait will provide you methods so you can control the data before and after it touch the database in large scale, using optimized statements for better performance.  
The methods will only be used if defined, so you can define all your Models to be using this trait and only when they are defined they will be used.  
If the methods are not present and the Model also implements the Triggable trait's methods, a operation per-record will be used intead.  
But if no Mass method and no Triggable's method is present, then the Laravel's default actions will be used.

List of allowed triggers:


* beforeMassCreate(array $values)
* afterMassCreated(array $values)
* beforeMassCreateUsing($query, array $values)
* afterMassCreatedUsing($query, array $values)
* beforeMassUpdate($query, array $values)
* afterMassUpdated($query, array $values)
* beforeMassDelete($query)
* afterMassDeleted($query)
* beforeMassForceDelete($query) ¹
* afterMassForceDeleted($query) ¹

¹ only available when using "SoftDeletes"

### Validatable

Creates a set of methods and definitions to you create system-wide validation rules for your models.
For example, a Users table contains the "email" column `not null`. In every single place you can create or update a user's email you should
need to validate this same rule: `'email' => 'required'`. But why? this completely breaks the sense of reusability and allows a lot of mistakes.
Using this trait you define in Model-level this rule, and into every and any operation, this rule will be validated.

This trait also provides rules for each operation as "createRules", "updateRules" and "deleteRules", and permits your to define a Mutator to the rule by
using the "get<studly attr name>AttributeRule" method, wich will receive the list of rules and the operation, and must return the updated list of rules, for example:

```php
public function getNameAttributeRule($rules, $operation)
{
   return $rules;
}
```

It is also possible to define your rules in a different class, all you need is to extend the Rules abstract and define your Validatable model with the class name, for example:

```php
public $rules = \App\Models\Rules\MyModelRule::class;
```

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

A new-and-useful function for laravel's Builder to do not duplicate unique joins, configurable through `modelling.database.smarty_joins`.
This will allow you to put joins on queries using whatever logic you want, and if they are completely equal, the builder will not duplicate them. For example:
```php
$query = MyModel::where('ative', '=', true);

if ($filter1) {
   $query->join('table2', 'table2.id', '=', 'mytable.id_table2')
      ->where('table2.filtered', '=', $value);
}
if ($anotherFilter) {
   $query->join('table2', 'table2.id', '=', 'mytable.id_table2')
      ->where('table2.another_column', '=', $value);
}
```
On raw laravel's builder, this would generate a duplicate join clause for the "table2". Using the smarty_joins, will only apply ONE join, and BOTH conditions!.

## Persistable

This one is a trait that allows you to quickly add "insert", "update" and "delete" to any class that you wish to control those operations, for example, in your Controller.
By adding this trait to your controller and creating a property `$model` containing the path to the model class, you will have an easy to use collection of methods for persistance.

List of methods:

* insert(array $data)
* update(array $data, $ids) ¹
* updateOrInsert(array $data)
* delete($ids) ¹

¹ the `$ids` can be either a single identifier or a list of identifiers to perform a bulk operation.
