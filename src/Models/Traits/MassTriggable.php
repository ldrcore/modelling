<?php

namespace LDRCore\Modelling\Models\Traits;
use LDRCore\Modelling\Eloquent\Builder;

/**
 * Trait MassTriggable
 * Defines and contains the availability to the model perform mass operations with triggers and performance
 *
 * @method beforeMassCreate(array $values) Trigger that is execute before mass inserts
 * @method afterMassCreate(array $values) Trigger that is execute after mass inserts
 * @method beforeMassCreateUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
 * @method afterMassCreateUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
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
        return new Builder($query);
    }
}
