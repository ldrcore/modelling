<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Str;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;

/**
 * Trait Validatable
 * @property array $rules
 * @property array $createRules
 * @property array $updateRules
 * @property array $deleteRules
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
	
	public function getRules($operation) : array
	{
		$result = [];
		$base = $this->rules ?? [];
		switch (Str::lower($operation)) {
			case 'c':
				$current = $this->createRules ?? [];
				break;
			case 'u':
				$current = $this->updateRules ?? [];
				break;
			case 'd':
				$current = $this->deleteRules ?? [];
				break;
			default:
				$current = [];
				break;
		}
		foreach ($current as $name => $rule) {
			$sep = Arr::exists($base, $name) ? "|" : "";
			$result[$name] = $rule . $sep . $base[$name] ?? "";
		}
		return $result;
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
	
	public function getValidator($operation): ValidatorContract
	{
		return Validator::make($this->getValidationData(), $this->getRules($operation), $this->getMessages(), $this->getLabels());
	}
	
	public function validate($operation = 'c')
	{
		$this->beforeValidate();
		$v = $this->getValidator($operation);
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
