<?php

namespace LDRCore\Modelling\Eloquent\Traits;

trait Modeller
{
	static $mass = false;
	
	protected function iterateAsCursor(\Closure $procedure)
	{
		self::$mass = true;
		$count = 0;
		$this->getConnection()->beginTransaction();
		foreach ((clone $this)->cursor() as $model) {
			if (call_user_func_array($procedure, [$model]) !== false) {
				$count++;
			}
		}
		$this->getConnection()->commit();
		self::$mass = false;
		return $count;
	}
}