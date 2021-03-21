<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\Validatable;

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
		if ($this->hasMassTriggableMethod(['beforeMassUpdate', 'afterMassUpdated'])) {
            if (has_trait($this->model, Validatable::class)) {
                $this->validate($values, Validatable::$UPDATED);
            }
			return $this->executeTriggers($values);
		} elseif ($this->hasTriggableMethod(['beforeUpdate', 'afterUpdated']) && self::$mass === false) {
			return $this->updateUsingModel($values);
		}
		if (has_trait($this->model, Validatable::class)) {
		    $this->validate($values, Validatable::$UPDATED);
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
		method_exists($this->model, 'afterMassUpdated') ? $this->model->afterMassUpdated($this, $values) : null;
		return $result;
	}
}
