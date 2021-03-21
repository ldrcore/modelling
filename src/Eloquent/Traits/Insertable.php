<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use LDRCore\Modelling\Models\Traits\Validatable;

trait Insertable
{
	use Modeller;

    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
	public function insert(array $values)
	{
		if ($this->hasMassTriggableMethod(['beforeMassCreate', 'afterMassCreated'])) {
            if (has_trait($this->model, Validatable::class)) {
                $this->validate($values, Validatable::$CREATED);
            }
			return $this->executeInsertTriggers($values);
		} elseif ($this->hasTriggableMethod(['beforeCreate', 'afterCreated']) && self::$mass === false) {
			return $this->insertModel($values) > 0;
		}
		if (has_trait($this->model, Validatable::class)) {
		    $this->validate($values, Validatable::$CREATED);
        }
		return parent::insert($values);
	}
    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array  $values
     * @param  string|null  $sequence
     * @return int
     */
	public function insertGetId(array $values, $sequence = null)
	{
		if ($this->hasMassTriggableMethod(['beforeMassCreate', 'afterMassCreated'])) {
            if (has_trait($this->model, Validatable::class)) {
                $this->validate($values, Validatable::$CREATED);
            }
			return $this->executeInsertGetIdTriggers($values);
		} elseif ($this->hasTriggableMethod(['beforeCreate', 'afterCreated']) && self::$mass === false) {
			return $this->insertModel($values);
		}
		if (has_trait($this->model, Validatable::class)) {
		    $this->validate($values, Validatable::$CREATED);
        }
		return parent::insertGetId($values, $sequence);
	}
    /**
     * Insert a new record into the database while ignoring errors.
     *
     * @param  array  $values
     * @return int
     */
	public function insertOrIgnore(array $values)
	{
		if ($this->hasMassTriggableMethod(['beforeMassCreate', 'afterMassCreated'])) {
            if (has_trait($this->model, Validatable::class)) {
                $this->validate($values, Validatable::$CREATED,true);
            }
			return $this->executeInsertOrIgnoreTriggers($values);
		} elseif ($this->hasTriggableMethod(['beforeCreate', 'afterCreated']) && self::$mass === false) {
			$this->insertModel($values,  true);
			return true;
		}
        if (has_trait($this->model, Validatable::class)) {
            $this->validate($values, Validatable::$CREATED, true);
        }
		return parent::insertOrIgnore($values);
	}
    /**
     * Insert new records into the table using a subquery.
     *
     * @param  array  $columns
     * @param  \Closure|\Illuminate\Database\Query\Builder|string  $query
     * @return int
     */
	public function insertUsing(array $columns, $query)
	{
		if ($this->hasMassTriggableMethod(['beforeMassCreateUsing', 'afterMassCreatedUsing'])) {
            if (has_trait($this->model, Validatable::class)) {
                $this->validate($values, Validatable::$CREATED);
            }
			return $this->executeInsertUsingTriggers($columns, $query);
		} elseif ($this->hasTriggableMethod(['beforeCreate', 'afterCreated']) && self::$mass === false) {
			return $this->insertUsingModel($columns, $query);
		}
        if (has_trait($this->model, Validatable::class)) {
            $this->validate($values, Validatable::$CREATED);
        }
		return parent::insertUsing($columns, $query);
	}
	/**
	 * Insert the given data using a Model instance
	 * @param array $values
	 * @param boolean $ignore
	 * @return false
	 */
	private function insertModel(array $values, $ignore = false)
	{
        if (! is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }
        $last = null;
        foreach ($values as $row) {
	        $model = new $this->model;
	        foreach ($row as $c => $v) {
	        	$model->{$c} = $v;
	        }
	        try{
		        $last = $model->save();
			} catch (\PDOException $e) {
			}
        }
        return $last->id;
	}
	/**
	 * Perform the "insertUsing" operations using a Model instance
	 * @param array $values
	 * @param  \Closure|\Illuminate\Database\Query\Builder|string  $query
	 * @return int
	 */
	private function insertUsingModel(array $columns, $query = null)
	{
		return $this->iterateAsCursor(function ($object) use ($columns) {
			$model = new $this->model;
			foreach ($columns as $name) {
				$model->{$name} = $object->{$name} ?? null;
			}
			return $model->save();
		}, $query);
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeInsertTriggers(array $values)
	{
		method_exists($this->model, 'beforeMassCreate') ? $this->model->beforeMassCreate($values) : null;
		$result = parent::insert($values);
		method_exists($this->model, 'afterMassCreated') ? $this->model->afterMassCreated($values) : null;
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeInsertGetIdTriggers(array $values)
	{
		method_exists($this->model, 'beforeMassCreate') ? $this->model->beforeMassCreate($values) : null;
		$result = parent::insertGetId($values);
		method_exists($this->model, 'afterMassCreated') ? $this->model->afterMassCreated($values) : null;
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeinsertOrIgnoreTriggers(array $values)
	{
		method_exists($this->model, 'beforeMassCreate') ? $this->model->beforeMassCreate($values) : null;
		$result = parent::insertOrIgnore($values);
		method_exists($this->model, 'afterMassCreated') ? $this->model->afterMassCreated($values) : null;
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $columns
	 * @return int
	 */
	private function executeInsertUsingTriggers(array $columns, $query)
	{
		method_exists($this->model, 'beforeMassCreateUsing') ? $this->model->beforeMassCreateUsing($query, $columns) : null;
		$result = parent::insertUsing($columns, $query);
		method_exists($this->model, 'afterMassCreatedUsing') ? $this->model->afterMassCreatedUsing($query, $columns) : null;
		return $result;
	}
}
