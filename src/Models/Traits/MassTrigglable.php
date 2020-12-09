<?php

namespace LDRCore\Modelling\Models\Traits;

trait MassTrigglable
{
	/**
	 * Trigger that is executed before mass inserts
	 */
	public function beforeMassCreate(array $values)
	{
	}
	/**
	 * Trigger that is executed after mass inserts
	 */
	public function afterMassCreate(array $values)
	{
	}
	/**
	 * Trigger that is executed before a insertUsing insert
	 */
	public function beforeMassCreateUsing($query, array $values)
	{
	}
	/**
	 * Trigger that is executed after a insertUsing insert
	 */
	public function afterMassCreateUsing($query, array $values)
	{
	}
	/**
	 * Trigger that is executed before mass updates
	 */
	public function beforeMassUpdate($query, array $values)
	{
	}
	/**
	 * Trigger that is executed after mass updates
	 */
	public function afterMassUpdate($query, array $values)
	{
	}
	/**
	 * Trigger that is executed before mass deletes
	 */
	public function beforeMassDelete($query)
	{
	}
	/**
	 * Trigger that is executed after mass deletes
	 */
	public function afterMassDelete($query)
	{
	}
	/**
	 * Trigger that is executed before mass force deletes
	 */
	public function beforeMassForceDelete($query)
	{
	}
	/**
	 * Trigger that is executed after mass force deletes
	 */
	public function afterMassForceDelete($query)
	{
	}
}
