<?php

namespace LDRCore\Modelling\Models\Traits;
/**
 * Trait MassTriggable
 * Defines and contains the availability to the model perform mass operations with triggers and performance
 *
 * @method beforeMassCreate(array $values) Trigger that is execute before mass inserts
 * @method afterMassCreate(array $values) Trigger that is execute after mass inserts
 * @method beforeMassCreateUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
 * @method afterMassCreateUsing(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after a insertUsing insert
 * @method beforeMassUpdate(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed before mass updates
 * @method afterMassUpdate(\Closure|\Illuminate\Database\Query\Builder|string $query, array $values) Trigger that is executed after mass updates
 * @method beforeMassDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed before mass deletes
 * @method afterMassDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed after mass deletes
 * @method beforeMassForceDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed before mass forceDeletes
 * @method afterMassForceDelete(\Closure|\Illuminate\Database\Query\Builder|string $query) Trigger that is executed after mass forceDeletes
 *
 * @package LDRCore\Modelling\Models\Traits
 */
trait MassTriggable
{
}
