<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use LDRCore\Modelling\Eloquent\Builder;

trait Triggable
{
    private $base_attribute = [];
    private $originals = [];
    private $restoring = false;
    
	public static function bootTriggable()
	{
		static::creating(function(self $model){
			$model->beforeCreate();
            $model->filterDatabaseArgs();
		});
		static::created(function(self $model){
			$model->restoreBaseArgs();
			$model->afterCreated();
		});
		static::updating(function(self $model){
			if (has_trait($model, SoftDeletes::class) && $model->restoring) {
				$model->beforeRestore();
			} else {
				$model->beforeUpdate();
			}
			$model->originals = $model->getOriginal();
            $model->filterDatabaseArgs();
		});
		static::updated(function(self $model){
			$model->restoreBaseArgs();
			$changes = self::computeChanges($model);
			if (has_trait($model, SoftDeletes::class) && $model->restoring) {
				$model->afterRestored($changes);
			} else {
				$model->afterUpdated($changes);
			}
		});
		static::deleting(function(self $model){
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
		});
		static::deleted(function(self $model){
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
		});
		static::registerModelEvent('restoring', function (self $model) {
			$model->restoring = true;
			$model->beginTransaction();
			$model->beforeRestore();
			$model->filterDatabaseArgs();
		});
		static::registerModelEvent('restored', function (self $model) {
			$model->restoreBaseArgs();
			$model->afterRestored();
			$model->commit();
			$model->restoring = false;
		});
		static::saving(function (self $model) {
			$model->beginTransaction();
		});
		static::saved(function (self $model) {
			$model->commit();
		});
		static::updating(function (self $model) {
			$model->beginTransaction();
		});
		static::updated(function (self $model) {
			$model->commit();
		});
		static::deleting(function (self $model) {
			$model->beginTransaction();
		});
		static::deleted(function (self $model) {
			$model->commit();
		});
	}
	
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
	
	protected function filterDatabaseArgs()
	{
		$this->base_attribute = $this->attributes;
		$cols = Schema::getColumnListing($this->getTable());
		foreach ($this->attributes as $key => $value) {
			if (!in_array($key, $cols)) {
				unset($this->attributes[$key]);
			}
		}
	}
	
	protected function restoreBaseArgs()
	{
		$this->attributes = array_merge($this->attributes, $this->base_attribute);
		unset($this->oldAttrs);
	}
	
	protected static function computeChanges(self $model)
	{
		$changes = [];
		foreach ($model->getChanges() as $attribute => $value) {
			$changes[$attribute] = ['old' => $model->originals[$attribute], 'new' => $value];
		}
		return $changes;
	}
	
	public function beginTransaction()
    {
        $conn = $this->getConnection();
        $conn && $conn->beginTransaction();
    }
    
    public function commit()
    {
        $conn = $this->getConnection();
        $conn && $conn->commit();
    }
    
    public function rollBack()
    {
        $conn = $this->getConnection();
        $conn && $conn->rollBack();
    }
    
	protected function beforeCreate()
	{
	}
	
	protected function afterCreated()
	{
	}
	
	protected function beforeUpdate()
	{
	}
	
	protected function afterUpdated($changes = [])
	{
	}
	
	protected function beforeDelete()
	{
	}
	
	protected function afterDeleted()
	{
	}
	
	protected function beforeRestore()
	{
	}
	
	protected function afterRestored($changes = [])
	{
	}
	
	protected function beforeForceDelete()
	{
	}
	
	protected function afterForceDeleted()
	{
	}
}