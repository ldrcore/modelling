<?php

namespace LDRCore\Modelling\Models\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TriggableObserver
 * Manage and Provides usability to Model classes to perform actions before and after database alterations
 *
 * @method beforeCreate(Model $model) Trigger that is executed before the model is created
 * @method afterCreated(Model $model) Trigger that is executed after the model is created
 * @method beforeUpdate(Model $model) Trigger that is executed before the model is updated
 * @method afterUpdated(Model $model) Trigger that is executed after the model is updated. The changes are acessible with $model->_changes property.
 * @method beforeDelete(Model $model) Trigger that is executed before the model is deleted
 * @method afterDeleted(Model $model) Trigger that is executed after the model is deleted
 * @method beforeRestore(Model $model) Trigger that is executed before the model is restored
 * @method afterRestored(Model $model) Trigger that is executed after the model is restored. The changes are acessible with $model->_changes property.
 * @method beforeForceDelete(Model $model) Trigger that is executed before the model is trully deleted
 * @method afterForceDeleted(Model $model) Trigger that is executed after the model is trully deleted
 *
 * @package LDRCore\Modelling\Models\Observers
 */
class TriggableObserver
{
	/**
	 * Call the method based on it's existance
	 * @param $model
	 * @param $method
	 * @param array $changes
	 */
	private function callHandler(Model $model, $method, $changes = [])
	{
		if (method_exists($model, $method)) {
			$model->{$method}($changes);
		} else {
			if (!empty($changes)) {
				$model->_changes = $changes;
			}
			try {
				$model->fireModelEvent($method, false);
			} catch (\BadMethodCallException $e) {
				// Ignore unregistered events.
			}
		}
	}
	/**
	 * Listening the changes before the model is created
	 * @param Model $model
	 */
	public function creating(Model $model)
	{
		$this->callHandler($model, 'beforeCreate');
        $model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is created
	 * @param Model $model
	 */
	public function created(Model $model)
	{
		$model->restoreBaseArgs();
		$this->callHandler($model, 'afterCreated');
	}
	/**
	 * Listening the changes before the model is updated
	 * @param Model $model
	 */
	public function updating(Model $model)
	{
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			$this->callHandler($model, 'beforeRestore');
		} else {
			$this->callHandler($model, 'beforeUpdate');
		}
		$model->originals = $model->getOriginal();
        $model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is updated
	 * @param Model $model
	 */
	public function updated(Model $model)
	{
		$model->restoreBaseArgs();
		$changes = self::computeChanges($model);
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			$this->callHandler($model, 'afterRestored', $changes);
		} else {
			$this->callHandler($model, 'afterUpdated', $changes);
		}
	}
	/**
	 * Listening the changes before the model is deleted
	 * @param Model $model
	 */
	public function deleting(Model $model)
	{
		$model->beginTransaction();
		if (has_trait($model, SoftDeletes::class)) {
			if ($model->forceDeleting) {
				$this->callHandler($model, 'beforeForceDelete');
			} else {
				$this->callHandler($model, 'beforeDelete');
			}
		}  else {
			$this->callHandler($model, 'beforeDelete');
		}
		$model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is deleted
	 * @param Model $model
	 */
	public function deleted(Model $model)
	{
		if (has_trait($model, SoftDeletes::class)) {
			if ($model->forceDeleting) {
				$this->callHandler($model, 'afterForceDeleted');
			} else {
				$this->callHandler($model, 'afterDeleted');
			}
		} else {
			$this->callHandler($model, 'afterDeleted');
		}
		$model->restoreBaseArgs();
		$model->commit();
	}
	/**
	 * Listening the changes before the model is restored
	 * @param Model $model
	 */
	public function restoring(Model $model)
	{
		$model->restoring = true;
	}
	/**
	 * Listening the changes after the model is restored
	 * @param Model $model
	 */
	public function restored(Model $model)
	{
		$model->restoring = false;
	}
	/**
	 * Listening the changes before the model is saved
	 * @param Model $model
	 */
	public function saving(Model $model)
	{
		$model->beginTransaction();
	}
	/**
	 * Listening the changes after the model is saved
	 * @param Model $model
	 */
	public function saved(Model $model)
	{
		$model->commit();
	}
	/**
	 * Compute changes before and after the update
	 * @param Model $model
	 * @return array
	 */
	protected static function computeChanges(Model $model)
	{
		$changes = [];
		foreach ($model->getChanges() as $attribute => $value) {
			$changes[$attribute] = ['old' => $model->originals[$attribute], 'new' => $value];
		}
		return $changes;
	}
}
