<?php

namespace LDRCore\Modelling\Eloquent;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Config;
use LDRCore\Modelling\Traits\Triggable;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
	static $mass = false;
	
	public function update(array $values)
	{
		if (has_trait($this->model, Triggable::class) && self::$mass === false) {
			return $this->updateUsingModel($values);
		}
		return parent::update($values);
	}
	
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
	
	protected function iterateAsCursor(\Closure $procedure)
	{
		self::$mass = true;
		$count = 0;
		$this->getConnection()->beginTransaction();
		foreach ((clone $this)->cursor() as $model) {
			if (call_user_func_array($procedure, [$model]) !== false) {
				$count++;
			}
		}
		$this->getConnection()->commit();
		self::$mass = false;
		return $count;
	}
	
	public function updateUsingModel(array $values)
	{
		return $this->iterateAsCursor(function ($model) use ($values) {
			foreach ($values as $key => $value) {
				$model->{$key} = $value;
			}
			return $model->save();
		});
	}
	
	public function deleteUsingModel($force = false)
	{
		return $this->iterateAsCursor(function ($model) use ($force) {
			return $force ? $model->forceDelete() : $model->delete();
		});
	}

    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        if (!$this->alreadyJoined($table, $first, $operator, $second)) {
            return parent::join($table, $first, $operator, $second, $type, $where);
        }
        return $this;
    }

    protected function alreadyJoined($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
    	if (Config::get('modelling.database.smarty_joins', false)) {
		    $contains = false;
		    $current = null;
		    foreach ($this->joins ?? [] as $clause) {
			    /** @var JoinClause $clause */
			    if ($clause->table == $table) {
				    $contains = true;
				    $current = $clause;
				    break;
			    }
		    }
		    if ($contains) {
			    $join = $this->newJoinClause($this, $type, $table);
			    if ($first instanceof \Closure) {
				    $first($join);
			    } else {
				    $method = $where ? 'where' : 'on';
				    $join = $join->$method($first, $operator, $second);
			    }
			    return $join->getBindings() == $current->getBindings() && $join->wheres == $current->wheres;
		    }
		    return false;
	    }
    	return true;
    }
}
