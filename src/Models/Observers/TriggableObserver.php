<?php

namespace LDRCore\Modelling\Models\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriggableObserver
{
	/**
	 * Listening the changes before the model is created
	 * @param Model $model
	 */
	public function creating(Model $model)
	{
		$model->beforeCreate();
        $model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is created
	 * @param Model $model
	 */
	public function created(Model $model)
	{
		$model->restoreBaseArgs();
		$model->afterCreated();
	}
	/**
	 * Listening the changes before the model is updated
	 * @param Model $model
	 */
	public function updating(Model $model)
	{
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			$model->beforeRestore();
		} else {
			$model->beforeUpdate();
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
			$model->afterRestored($changes);
		} else {
			$model->afterUpdated($changes);
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
				$model->beforeForceDelete();
			} else {
				$model->beforeDelete();
			}
		}  else {
			$model->beforeDelete();
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
				$model->afterForceDeleted();
			} else {
				$model->afterDeleted();
			}
		} else {
			$model->afterDeleted();
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
		$model->beginTransaction();
		$model->beforeRestore();
		$model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is restored
	 * @param Model $model
	 */
	public function restored(Model $model)
	{
		$model->restoreBaseArgs();
		$model->afterRestored();
		$model->commit();
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
