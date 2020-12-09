<?php

namespace LDRCore\Modelling\Eloquent\Traits;

use LDRCore\Modelling\Models\Traits\MassTrigglable;
use LDRCore\Modelling\Models\Traits\Triggable;

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
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeInsertTriggers($values);
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->insertModel($values) > 0;
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
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeInsertGetIdTriggers($values);
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->insertModel($values);
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
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeInsertOrIgnoreTriggers($values);
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
			$this->insertModel($values, true);
			return true;
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
		if (has_trait($this->model, MassTrigglable::class)) {
			return $this->executeInsertUsingTriggers($columns, $query);
		} elseif (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->insertUsingModel($columns, $query);
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
		$this->model->beforeMassInsert($values);
		$result = parent::insert($values);
		$this->model->afterMassInsert($values);
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeInsertGetIdTriggers(array $values)
	{
		$this->model->beforeMassInsert($values);
		$result = parent::insertGetId($values);
		$this->model->afterMassInsert($values);
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $values
	 * @return int
	 */
	private function executeinsertOrIgnoreTriggers(array $values)
	{
		$this->model->beforeMassInsert($values);
		$result = parent::insertOrIgnore($values);
		$this->model->afterMassInsert($values);
		return $result;
	}
	/**
	 * Execute the operation using the Mass triggers
	 * @param array $columns
	 * @return int
	 */
	private function executeInsertUsingTriggers(array $columns, $query)
	{
		$this->model->beforeMassInsertUsing($query, $columns);
		$result = parent::insertUsing($columns, $query);
		$this->model->afterMassInsertUsing($query, $columns);
		return $result;
	}
}
