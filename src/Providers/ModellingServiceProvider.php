<?php

namespace LDRCore\Modelling\Providers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use LDRCore\Modelling\Connections\MysqlConnection;
use LDRCore\Modelling\Console\Command\ObserverMakeCommand;

class ModellingServiceProvider extends ServiceProvider
{
	private $configPath;
	/**
	 * ModellingServiceProvider constructor.
	 * @param $app
	 */
	public function __construct($app)
	{
		parent::__construct($app);
		$this->configPath = __DIR__ . '/../modelling.php';
	}
	/**
	 * Register usable configurations
	 */
	public function register()
    {
    	$this->registerHelpers();
    	// Configuration files
	    $this->mergeConfigFrom( $this->configPath, 'modelling' );
    }
	/**
	 * Boot all necessary operations
	 */
    public function boot()
    {
    	// Register commands
	    if ($this->app->runningInConsole()) {
	        $this->commands([
	            ObserverMakeCommand::class,
	        ]);
	    }
    	// Publish our config
        $this->publishes([
        	$this->configPath => $this->app->configPath('modelling.php')
        ]);
        // Connections
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
        	$class = Config::get('modelling.database.connection.mysql', MysqlConnection::class);
            return new $class($connection, $database, $prefix, $config);
        });
    }
	/**
	 * Register the package's helpers
	 */
    private function registerHelpers()
    {
    	if (is_file(__DIR__.'/../helpers.php')) {
    		require_once __DIR__ . '/../helpers.php';
	    }
    }
}
