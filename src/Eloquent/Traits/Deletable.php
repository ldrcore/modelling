<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\Triggable;

trait Deletable
{
	use Modeller;
	
	public function delete()
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->deleteUsingModel(false);
		}
		return parent::delete();
	}
	
	public function forceDelete()
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->deleteUsingModel(true);
		}
		return parent::forceDelete();
	}
	
	public function deleteUsingModel($force = false)
	{
		return $this->iterateAsCursor(function ($model) use ($force) {
			return $force ? $model->forceDelete() : $model->delete();
		});
	}
}
