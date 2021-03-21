<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
	/**
	 * Determine if the current model can perform MassTriggable operations
	 * @param array $methods
	 * @return bool
	 */
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
	/**
	 * Determine if the current model can perform Triggable operations
	 * @param array $methods
	 * @return bool
	 */
	protected function hasTriggableMethod($methods = [])
	{
		if (has_trait($this->model, Triggable::class)) {
			foreach ($methods as $name) {
				if (method_exists($this->model, $name) || $this->model->hasRegisteredEvent($name)) {
					return true;
				}
			}
		}
		return false;
	}
    /**
     * @param array $values
     * @param string $operation
     * @param false $ignore
     */
	protected function validate(array $values, $operation, $ignore = false)
    {
        try {
            $this->model->massValidate($values, $operation);
        } catch (ValidationException $v) {
            if (!$ignore) {
                throw $v;
            }
            foreach ($v->errors() as $key => $error) {
                $index = Str::substr($key, 0, strpos($key, '.'));
                unset($values[$index]);
            }
        }
        return $values;
    }
}
