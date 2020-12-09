<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\MassTrigglable;
use LDRCore\Modelling\Models\Traits\Triggable;

trait Deletable
{
	use Modeller;
    /**
     * Delete a record from the database.
     * @return mixed
     */
	public function delete()
	{
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeDeleteTriggers();
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
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
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeForceDeleteTriggers();
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
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
		$this->model->beforeMassDelete($this);
		$result = parent::delete();
		$this->model->afterMassDelete($this);
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @return int
	 */
	private function executeForceDeleteTriggers()
	{
		$this->model->beforeMassForceDelete($this);
		$result = parent::forceDelete();
		$this->model->afterMassForceDelete($this);
		return $result;
	}
}
