<?php

namespace LDRCore\Modelling\Rules;

class Unique extends \Illuminate\Validation\Rules\Unique
{
	public static function table($table, $column = 'NULL')
	{
		return new self($table, $column);
	}
	
	public function __toString()
	{
		if ($this->ignore instanceof \Closure) {
			$this->ignore = call_user_func_array($this->ignore);
		}
		return parent::__toString();
	}
}
