<?php

namespace LDRCore\Modelling\Connections;

use LDRCore\Modelling\Connections\Traits\HandlesTransactionsHooks;
use LDRCore\Modelling\Eloquent\Blueprint;

class MysqlConnection extends \Illuminate\Database\MySqlConnection
{
	use HandlesTransactionsHooks;
	
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
    	$builder = parent::getSchemaBuilder();
    	$builder->blueprintResolver(function ($table, $callback) {
    		return new Blueprint($table, $callback);
	    });
    	return $builder;
    }
}
