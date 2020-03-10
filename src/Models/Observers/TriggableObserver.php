<?php

namespace LDRCore\Modelling\Models\Observers;

use Illuminate\Database\Eloquent\SoftDeletes;
use LDRCore\Modelling\Models\Traits\Triggable;

class TriggableObserver
{
	public function creating(Triggable $model){
		$model->beforeCreate();
        $model->filterDatabaseArgs();
	}
	
	public function created(Triggable $model){
		$model->restoreBaseArgs();
		$model->afterCreated();
	}
	
	public function updating(Triggable $model){
		$model->beginTransaction();
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			$model->beforeRestore();
		} else {
			$model->beforeUpdate();
		}
		$model->originals = $model->getOriginal();
        $model->filterDatabaseArgs();
	}
	
	public function updated(Triggable $model){
		$model->restoreBaseArgs();
		$changes = self::computeChanges($model);
		if (has_trait($model, SoftDeletes::class) && $model->restoring) {
			$model->afterRestored($changes);
		} else {
			$model->afterUpdated($changes);
		}
		$model->commit();
	}
	
	public function deleting(Triggable $model){
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
	
	public function deleted(Triggable $model){
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
	
	public function restoring(Triggable $model) {
		$model->restoring = true;
		$model->beginTransaction();
		$model->beforeRestore();
		$model->filterDatabaseArgs();
	}
	
	public function restored(Triggable $model) {
		$model->restoreBaseArgs();
		$model->afterRestored();
		$model->commit();
		$model->restoring = false;
	}
	
	public function saving(Triggable $model) {
		$model->beginTransaction();
	}
	
	public function saved(Triggable $model) {
		$model->commit();
	}
}