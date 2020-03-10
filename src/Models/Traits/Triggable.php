<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Support\Facades\Schema;
use LDRCore\Modelling\Eloquent\Builder;
use LDRCore\Modelling\Models\Observers\TriggableObserver;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;

trait Triggable
{
    public $base_attribute = [];
    
	public static function bootTriggable()
	{
		static::observe(TriggableObserver::class);
		// Dispatch Validation always AFTER our own observer.
		if (has_trait(new static, Validatable::class)) {
			static::observe(ValidatableObserver::class);
		}
	}
	
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
	
	public function filterDatabaseArgs()
	{
		$this->base_attribute = $this->attributes;
		$cols = Schema::getColumnListing($this->getTable());
		foreach ($this->attributes as $key => $value) {
			if (!in_array($key, $cols)) {
				unset($this->attributes[$key]);
			}
		}
	}
	
	public function restoreBaseArgs()
	{
		$this->attributes = array_merge($this->attributes, $this->base_attribute);
		unset($this->oldAttrs);
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
    
	public function beforeCreate()
	{
	}
	
	public function afterCreated()
	{
	}
	
	public function beforeUpdate()
	{
	}
	
	public function afterUpdated($changes = [])
	{
	}
	
	public function beforeDelete()
	{
	}
	
	public function afterDeleted()
	{
	}
	
	public function beforeRestore()
	{
	}
	
	public function afterRestored($changes = [])
	{
	}
	
	public function beforeForceDelete()
	{
	}
	
	public function afterForceDeleted()
	{
	}
}