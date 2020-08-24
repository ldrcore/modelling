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
    | Authentication Language Lines
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
		'smarty_joins' => true
	],
];
