<?php

namespace LDRCore\Modelling\Providers;

use Illuminate\Support\ServiceProvider;

class ModellingServiceProvider extends ServiceProvider
{
    public function register()
    {
    	$this->registerHelpers();
    }

    public function boot()
    {
    }
    
    private function registerHelpers()
    {
    	if (is_file(__DIR__.'/../helpers.php')) {
    		require_once __DIR__ . '/../helpers.php';
	    }
    }
}
