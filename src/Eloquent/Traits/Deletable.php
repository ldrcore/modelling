<?php

namespace LDRCore\Modelling\Eloquent\Traits;

trait Deletable
{
	use Modeller;
    /**
     * Delete a record from the database.
     * @return mixed
     */
	public function delete()
	{
		if ($this->hasMassTriggableMethod(['beforeMassDelete', 'afterMassDeleted'])) {
			return $this->executeDeleteTriggers();
		} elseif ($this->hasTriggableMethod(['beforeDelete', 'afterDeleted']) && self::$mass === false) {
			return $this->deleteUsingModel(false);
		}
		return parent::delete();
	}
    /**
     * Run the default delete function on the builder.
     * Since we do not apply scopes here, the row will actually be deleted.
     * @return mixed
     */
	public function forceDelete()
	{
		if ($this->hasMassTriggableMethod(['beforeMassForceDelete', 'afterMassForceDeleted'])) {
			return $this->executeForceDeleteTriggers();
		} elseif ($this->hasTriggableMethod(['beforeForceDelete', 'afterForceDeleted']) && self::$mass === false) {
			return $this->deleteUsingModel(true);
		}
		return parent::forceDelete();
	}
	/**
	 * Perform the delete operations using a Model instance.
	 * @param bool $force
	 * @return int
	 */
	public function deleteUsingModel($force = false)
	{
		return $this->iterateAsCursor(function ($model) use ($force) {
			return $force ? $model->forceDelete() : $model->delete();
		});
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @return int
	 */
	private function executeDeleteTriggers()
	{
		method_exists($this->model, 'beforeMassDelete') ? $this->model->beforeMassDelete($this) : null;
		$result = parent::delete();
		method_exists($this->model, 'afterMassDeleted') ? $this->model->afterMassDeleted($this) : null;
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @return int
	 */
	private function executeForceDeleteTriggers()
	{
		method_exists($this->model, 'beforeMassForceDelete') ? $this->model->beforeMassForceDelete($this) : null;
		$result = parent::forceDelete();
		method_exists($this->model, 'afterMassForceDeleted') ? $this->model->afterMassForceDeleted($this) : null;
		return $result;
	}
}
