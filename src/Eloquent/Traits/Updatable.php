<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\MassTriggable;
use LDRCore\Modelling\Models\Traits\Triggable;

trait Updatable
{
	use Modeller;
    /**
     * Update a record in the database.
     * @param  array  $values
     * @return int
     */
	public function update(array $values)
	{
		if (has_trait($this->model, MassTriggable::class) && (method_exists($this->model, 'beforeMassUpdate') || method_exists($this->model, 'afterMassUpdate'))) {
			return $this->executeTriggers($values);
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->updateUsingModel($values);
		}
		return parent::update($values);
	}
	/**
	 * Perform the update operations using a Model instance
	 * @param array $values
	 * @return int
	 */
	private function updateUsingModel(array $values)
	{
		return $this->iterateAsCursor(function ($model) use ($values) {
			foreach ($values as $key => $value) {
				$model->{$key} = $value;
			}
			return $model->save();
		});
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeTriggers(array $values)
	{
		method_exists($this->model, 'beforeMassUpdate') ? $this->model->beforeMassUpdate($this, $values) : null;
		$result = parent::update($values);
		method_exists($this->model, 'afterMassUpdate') ? $this->model->afterMassUpdate($this, $values) : null;
		return $result;
	}
}
