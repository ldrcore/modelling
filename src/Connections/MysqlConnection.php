<?php

namespace LDRCore\Modelling\Connections;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;
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
        	$class = Config::get('modelling.database.blueprint', Blueprint::class);
    		return new $class($table, $callback);
	    });
    	return $builder;
    }
    /**
     * Get a new query builder instance.
     *
     * @return Builder
     */
    public function query()
    {
    	$class = Config::get('modeeling.database.builder.builder', Builder::class);
        return new $class( $this, $this->getQueryGrammar(), $this->getPostProcessor() );
    }
}
