<?php

namespace LDRCore\Modelling\Eloquent;

use Illuminate\Support\Facades\Config;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestamps($precision = 0)
    {
        $this->timestamp(Config::get('modelling.model.timestamps.created_at'), $precision)->nullable();
        $this->timestamp(Config::get('modelling.model.timestamps.created_at'), $precision)->nullable();
    }
    
    /**
     * Add creation and update timestampTz columns to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestampsTz($precision = 0)
    {
        $this->timestamp(Config::get('modelling.model.timestamps.created_at'), $precision)->nullable();
        $this->timestamp(Config::get('modelling.model.timestamps.created_at'), $precision)->nullable();
    }
    
    /**
     * Indicate that the timestamp columns should be dropped.
     *
     * @return void
     */
    public function dropTimestamps()
    {
        $this->dropColumn(Config::get('modelling.model.timestamps.created_at'), Config::get('modelling.model.timestamps.created_at'));
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletes($column = null, $precision = 0)
    {
    	if ($column === null) {
    		$column = Config::get('modelling.model.timestamps.deleted_at');
	    }
        return $this->timestamp($column, $precision)->nullable();
    }
    
    /**
     * Add a "deleted at" timestampTz for the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletesTz($column = null, $precision = 0)
    {
    	if ($column === null) {
    		$column = Config::get('modelling.model.timestamps.deleted_at');
	    }
        return $this->timestampTz($column, $precision)->nullable();
    }
}
