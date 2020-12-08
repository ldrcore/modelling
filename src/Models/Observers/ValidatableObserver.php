<?php

namespace LDRCore\Modelling\Models\Observers;

use Illuminate\Database\Eloquent\Model;
use LDRCore\Modelling\Models\Traits\Validatable;

class ValidatableObserver
{
	/**
	 * Listening the changes before the model is saved
	 * @param Model $model
	 */
	public function saving(Model $model)
	{
		$model->validate(Validatable::$CREATED);
	}
	/**
	 * Listening the changes before the model is updated
	 * @param Model $model
	 */
	public function updating(Model $model)
	{
		$model->validate(Validatable::$UPDATED);
	}
	/**
	 * Listening the changes before the model is deleted
	 * @param Model $model
	 */
	public function deleting(Model $model)
	{
		$model->validate(Validatable::$DELETED);
	}
}
