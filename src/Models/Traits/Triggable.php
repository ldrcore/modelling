<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LDRCore\Modelling\Eloquent\Builder;
use LDRCore\Modelling\Models\Observers\TriggableObserver;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;

/**
 * Trait Triggable
 * Provides usability to Model classes to perform actions before and after database alterations
 *
 * @method beforeCreate() Trigger that is executed before the model is created
 * @method afterCreated() Trigger that is executed after the model is created
 * @method beforeUpdate() Trigger that is executed before the model is updated
 * @method afterUpdated($changes = []) Trigger that is executed after the model is updated with a list of changes applied in the update
 *    E.g.: [
 *             'name' => [
 *                'old' => 'My name',
 *                'new' => 'New name'
 *           ]
 *        ]
 * @method beforeDelete() Trigger that is executed before the model is deleted
 * @method afterDeleted() Trigger that is executed after the model is deleted
 * @method beforeRestore() Trigger that is executed before the model is restored
 * @method afterRestored($changes = []) Trigger that is executed after the model is restored with a list of changes applied in the update
 *    E.g.: [
 *             'name' => [
 *                'old' => 'My name',
 *                'new' => 'New name'
 *           ]
 *        ]
 * @method beforeForceDelete() Trigger that is executed before the model is trully deleted
 * @method afterForceDeleted() Trigger that is executed after the model is trully deleted
 *
 * @package LDRCore\Modelling\Models\Traits
 */
trait Triggable
{
    public $base_attribute = [];
	/**
	 * Boots current trait to register it's observers
	 */
	public static function bootTriggable()
	{
		self::initObserver();
		// Dispatch Validation always AFTER our own observer.
		if (has_trait(new static, Validatable::class)) {
			static::observe(ValidatableObserver::class);
		}
	}
	/**
	 * Initialize the observer for the trait
	 * @throws \Exception
	 */
	private static function initObserver()
	{
		$className = self::getObserverClass();
		$obj = new $className;
		if (!($obj instanceof TriggableObserver)) {
			throw new \Exception("Observer class must be an instance of TriggableObserver.");
		}
		static::observe($className);
	}
	/**
	 * Get the Observer class to be used
	 * @return string
	 */
	public static function getObserverClass() : string
	{
		return TriggableObserver::class;
	}
	/**
	 * Creater a builder instance
	 * @param $query
	 * @return Builder
	 */
    public function newEloquentBuilder($query)
    {
    	$class = Config::get('modeling.database.builder.builder', Builder::class);
        return new $class($query);
    }
	/**
	 * Filter the database columns out of the current attributes so the insert and update statements can work.
	 */
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
	/**
	 * Restore all the attributes filtered
	 */
	public function restoreBaseArgs()
	{
		$this->attributes = array_merge($this->attributes, $this->base_attribute);
		unset($this->oldAttrs);
	}
	/**
	 * Start a transaction or add a transaction level
	 */
	public function beginTransaction()
    {
        $conn = $this->getConnection();
        $conn && $conn->beginTransaction();
    }
	/**
	 * Commit the transaction or transaction level
	 */
    public function commit()
    {
        $conn = $this->getConnection();
        $conn && $conn->commit();
    }
	/**
	 * Rollback the transaction or transaction level
	 */
    public function rollBack()
    {
        $conn = $this->getConnection();
        $conn && $conn->rollBack();
    }
}
