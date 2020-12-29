<?php

namespace LDRCore\Modelling\Models\Traits;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use LDRCore\Modelling\Eloquent\Builder;

/**
 * Trait MassTriggable
 * Defines and contains the availability to the model perform mass operations with triggers and performance
 *
 * @method beforeMassCreate(array $values) Trigger that is execute before mass inserts
 * @method afterMassCreated(array $values) Trigger that is execute after mass inserts
 * @method beforeMassCreateUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
 * @method afterMassCreatedUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
 * @method beforeMassUpdate(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed before mass updates
 * @method afterMassUpdated(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after mass updates
 * @method beforeMassDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed before mass deletes
 * @method afterMassDeleted(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed after mass deletes
 * @method beforeMassForceDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed before mass forceDeletes
 * @method afterMassForceDeleted(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed after mass forceDeletes
 *
 * @package LDRCore\Modelling\Models\Traits
 */
trait MassTriggable
{
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
	 * Initialize trait's observer events.
	 */
	public function initializeMassTriggable()
	{
		$this->addObservableEvents([
			'beforeMassCreate',
			'afterMassCreated',
			'beforeMassCreateUsing',
			'afterMassCreatedUsing',
			'beforeMassUpdate',
			'afterMassUpdated',
			'beforeMassDelete',
			'afterMassDeleted',
			'beforeMassForceDelete',
			'afterMassForceDeleted',
		]);
	}
	/**
	 * Determine if current instance has registered the event
	 * @param $method
	 * @return bool
	 */
	public function hasRegistedEvent($method) : bool
	{
		return Arr::exists($this->dispatchesEvents ?? [], $method);
	}
}
