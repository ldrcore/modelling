<?php

namespace LDRCore\Modelling\Rules;

class Unique extends \Illuminate\Validation\Rules\Unique
{
	public function __toString()
	{
		if ($this->ignore instanceof \Closure) {
			$this->ignore = call_user_func_array($this->ignore);
		}
		return parent::__toString();
	}
}
