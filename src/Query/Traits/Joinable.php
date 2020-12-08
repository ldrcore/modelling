<?php

namespace LDRCore\Modelling\Query\Traits;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Config;
use LDRCore\Modelling\Eloquent\Builder;

trait Joinable
{
	/**
     * Add a join clause to the query.
     *
	 * @param string $table
	 * @param \Closure|string $first
	 * @param null $operator
	 * @param null $second
	 * @param string $type
	 * @param false $where
	 * @return $this|Builder
	 */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        if (!$this->alreadyJoined($table, $first, $operator, $second)) {
            return parent::join($table, $first, $operator, $second, $type, $where);
        }
        return $this;
    }
	/**
	 * Validates if a join is already made on the query
	 *
	 * @param $table
	 * @param $first
	 * @param null $operator
	 * @param null $second
	 * @param string $type
	 * @param false $where
	 * @return bool
	 */
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
