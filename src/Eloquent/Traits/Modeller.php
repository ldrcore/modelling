<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\MassTriggable;
use LDRCore\Modelling\Models\Traits\Triggable;

trait Modeller
{
	static $mass = false;
	/**
	 * Iterate current query as a cursor performing the operations on each record.
	 * @param \Closure $procedure
	 * @param  \Closure|\Illuminate\Database\Query\Builder|string  $query
	 * @return int
	 */
	protected function iterateAsCursor(\Closure $procedure, $query = null)
	{
		self::$mass = true;
		$count = 0;
		$this->getConnection()->beginTransaction();
		$query = $query ?? $this;
		foreach ((clone $query)->cursor() as $model) {
			if (call_user_func_array($procedure, [$model]) !== false) {
				$count++;
			}
		}
		$this->getConnection()->commit();
		self::$mass = false;
		return $count;
	}
	
	protected function hasMassTriggableMethod($methods = [])
	{
		if (has_trait($this->model, MassTriggable::class)) {
			foreach ($methods as $name) {
				if (method_exists($this->model, $name)) {
					return true;
				}
			}
		}
		return false;
	}
	
	protected function hasTriggableMethod($methods = [])
	{
		if (has_trait($this->model, Triggable::class)) {
			foreach ($methods as $name) {
				if (method_exists($this->model, $name)) {
					return true;
				}
			}
			$observer = new $this->model->getObserverClass();
			foreach ($methods as $name) {
				if (method_exists($observer, $name)) {
					return true;
				}
			}
		}
		return false;
	}
}
