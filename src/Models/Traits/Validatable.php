<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;

/**
 * Trait Validatable
 * @property array $rules
 * @property array $labels
 * @property array $messages
 * @package LDRCore\Modelling\Models\Traits
 */
trait Validatable
{
	public $errors = [];
	
	public static function bootValidatable()
	{
		if (!has_trait((new static), Triggable::class)) {
			static::observe(ValidatableObserver::class);
		}
	}
	
	public function getRules() : array
	{
		return $this->rules ?? [];
	}
	
	public function getLabels() : array
	{
		return $this->labels ?? [];
	}
	
	public function getMessages() : array
	{
		return $this->messages ?? [];
	}
	
	public function getValidationData() : array
	{
		if ($this instanceof Model) {
			return $this->attributes;
		}
		if ($this instanceof Arrayable) {
			return $this->toArray();
		}
		return [];
	}
	
	public function getValidator(): ValidatorContract
	{
		return Validator::make($this->getValidationData(), $this->getRules(), $this->getMessages(), $this->getLabels());
	}
	
	public function validate()
	{
		$this->beforeValidate();
		$v = $this->getValidator();
		$v->after(function () use ($v) {
			$this->errors = $v->errors();
		});
		$v->validate();
		$this->afterValidated($v);
	}
	
	public function beforeValidate()
	{
	}
	
	public function afterValidated(ValidatorContract $validator)
	{
	}
}