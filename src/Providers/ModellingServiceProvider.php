<?php

namespace LDRCore\Modelling\Providers;

use LDRCore\Modelling\Connections\MysqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use LDRCore\Modelling\Middlewares\HasAccess;

class ModellingServiceProvider extends ServiceProvider
{
    public function register()
    {
    	$this->registerHelpers();
    }

    public function boot()
    {
        // Connections
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MysqlConnection($connection, $database, $prefix, $config);
        });
    }
    
    private function registerHelpers()
    {
    	if (is_file(__DIR__.'/../helpers.php')) {
    		require_once __DIR__ . '/../helpers.php';
	    }
    }
}
