<?php

/*
|--------------------------------------------------------------------------
| Modelling configuration file
|--------------------------------------------------------------------------
|
| Allows developers to take control over some built-in functionalities.
|
*/

return [
	/*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
	|
	| Definitions for the Model class
	|
	*/
	'model' => [
		'timestamps' => [
			'created_at' => 'created_at',
			'updated_at' => 'updated_at',
			'deleted_at' => 'deleted_at'
		]
	],
	/*
    |--------------------------------------------------------------------------
    | Database configurations
    |--------------------------------------------------------------------------
	|
	| Definition for configurations related to database operations
	|
	*/
	'database' => [
		'connection' => [
			'mysql' => \LDRCore\Modelling\Connections\MysqlConnection::class,
		],
		'query' => [
			'builder' => \LDRCore\Modelling\Query\Builder::class,
			'eloquent' => \LDRCore\Modelling\Eloquent\Builder::class,
		],
		'blueprint' => \LDRCore\Modelling\Eloquent\Blueprint::class,
		'smarty_joins' => true
	],
];
