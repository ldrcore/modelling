<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\MassTrigglable;
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
		if (has_trait($this->model, MassTrigglable::class)) {
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
		$this->model->beforeMassUpdate($this, $values);
		$result = parent::update($values);
		$this->model->afterMassUpdate($this, $values);
		return $result;
	}
}
