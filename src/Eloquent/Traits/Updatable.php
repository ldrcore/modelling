<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\Triggable;

trait Updatable
{
	use Modeller;
	
	public function update(array $values)
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->updateUsingModel($values);
		}
		return parent::update($values);
	}
	
	private function updateUsingModel(array $values)
	{
		return $this->iterateAsCursor(function ($model) use ($values) {
			foreach ($values as $key => $value) {
				$model->{$key} = $value;
			}
			return $model->save();
		});
	}
}
