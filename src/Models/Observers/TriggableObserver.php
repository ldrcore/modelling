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
		method_exists($model, 'beforeCreate') ? $model->beforeCreate() : null;
        $model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is created
	 * @param Model $model
	 */
	public function created(Model $model)
	{
		$model->restoreBaseArgs();
		method_exists($model, 'afterCreated') ? $model->afterCreated() : null;
	}
	/**
	 * Listening the changes before the model is updated
	 * @param Model $model
	 */
	public function updating(Model $model)
	{
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			method_exists($model, 'beforeRestore') ? $model->beforeRestore() : null;
		} else {
			method_exists($model, 'beforeUpdate') ? $model->beforeUpdate() : null;
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
			method_exists($model, 'afterRestored') ? $model->afterRestored($changes) : null;
		} else {
			method_exists($model, 'afterUpdated') ? $model->afterUpdated($changes) : null;
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
				method_exists($model, 'beforeForceDelete') ? $model->beforeForceDelete() : null;
			} else {
				method_exists($model, 'beforeDelete') ? $model->beforeDelete() : null;
			}
		}  else {
			method_exists($model, 'beforeDelete') ? $model->beforeDelete() : null;
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
				method_exists($model, 'afterForceDeleted') ? $model->afterForceDeleted() : null;
			} else {
				method_exists($model, 'afterDeleted') ?  $model->afterDeleted() : null;
			}
		} else {
			method_exists($model, 'afterDeleted') ?  $model->afterDeleted() : null;
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
		method_exists($model, 'beforeRestore') ? $model->beforeRestore() : null;
		$model->filterDatabaseArgs();
	}
	/**
	 * Listening the changes after the model is restored
	 * @param Model $model
	 */
	public function restored(Model $model)
	{
		$model->restoreBaseArgs();
		method_exists($model, 'beforeRestored') ? $model->afterRestored() : null;
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
