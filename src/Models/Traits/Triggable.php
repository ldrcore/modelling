<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use LDRCore\Modelling\Eloquent\Builder;
use LDRCore\Modelling\Models\Observers\TriggableObserver;

trait Triggable
{
    public $base_attribute = [];
    public $originals = [];
    public $restoring = false;
    
	public static function bootTriggable()
	{
		static::observe(TriggableObserver::class);
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