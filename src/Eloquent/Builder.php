<?php

namespace LDRCore\Modelling\Eloquent;

use Illuminate\Database\Eloquent\Model;
use LDRCore\Modelling\Traits\Triggable;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
	static $mass = false;
	
	public function update(array $values)
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->updateUsingModel($values);
		}
		return parent::update($values);
	}
	
	public function delete()
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->deleteUsingModel(false);
		}
		return parent::delete();
	}
	
	public function forceDelete()
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->deleteUsingModel(true);
		}
		return parent::forceDelete();
	}
	
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
	
	public function updateUsingModel(array $values)
	{
		return $this->iterateAsCursor(function ($model) use ($values) {
			foreach ($values as $key => $value) {
				$model->{$key} = $value;
			}
			return $model->save();
		});
	}
	
	public function deleteUsingModel($force = false)
	{
		return $this->iterateAsCursor(function ($model) use ($force) {
			return $force ? $model->forceDelete() : $model->delete();
		});
	}
}