<?php


namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Support\Facades\Config;

trait Customizable
{
    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
	public function getCreatedAtColumn()
	{
		return Config::get('modelling.model.timestamps.created_at');
	}
    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
	public function getUpdatedAtColumn()
	{
		return Config::get('modelling.model.timestamps.updated_at');
	}
    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
	public function getDeleteddAtColumn()
	{
		return Config::get('modelling.model.timestamps.deleted_at');
	}
}
