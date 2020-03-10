<?php

namespace LDRCore\Modelling\Models\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriggableObserver
{
	public function creating(Model $model)
	{
		$model->beforeCreate();
        $model->filterDatabaseArgs();
	}
	
	public function created(Model $model)
	{
		$model->restoreBaseArgs();
		$model->afterCreated();
	}
	
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
	
	public function restoring(Model $model)
	{
		$model->restoring = true;
		$model->beginTransaction();
		$model->beforeRestore();
		$model->filterDatabaseArgs();
	}
	
	public function restored(Model $model)
	{
		$model->restoreBaseArgs();
		$model->afterRestored();
		$model->commit();
		$model->restoring = false;
	}
	
	public function saving(Model $model)
	{
		$model->beginTransaction();
	}
	
	public function saved(Model $model)
	{
		$model->commit();
	}
	
	protected static function computeChanges(Model $model)
	{
		$changes = [];
		foreach ($model->getChanges() as $attribute => $value) {
			$changes[$attribute] = ['old' => $model->originals[$attribute], 'new' => $value];
		}
		return $changes;
	}
}