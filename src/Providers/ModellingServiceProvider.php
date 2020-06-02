<?php

namespace LDRCore\Modelling\Providers;

use LDRCore\Modelling\Connections\MysqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use LDRCore\Modelling\Middlewares\HasAccess;

class ModellingServiceProvider extends ServiceProvider
{
	private $configPath;
	
	public function __construct($app)
	{
		parent::__construct($app);
		$this->configPath = __DIR__ . '/../modelling.php';
	}
	
	public function register()
    {
    	$this->registerHelpers();
    	// Configuration files
	    $this->mergeConfigFrom( $this->configPath, 'modelling' );
    }

    public function boot()
    {
    	// Publish our config
        $this->publishes([
        	$this->configPath => $this->app->configPath('modelling.php')
        ]);
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
