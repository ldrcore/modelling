<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Str;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;

trait Validatable
{
	// -------
	// > List of constants for definition
	public static $CREATED = 'c';
	public static $UPDATED = 'u';
	public static $DELETED = 'd';
	// --------
	/**
	 * Bag of erros happened in the current instance
	 * @var array
	 */
	public $errors = [];
	/**
	 * Boots current trait to register it's observers
	 */
	public static function bootValidatable()
	{
		if (!has_trait((new static), Triggable::class)) {
			static::observe(ValidatableObserver::class);
		}
	}
	/**
	 * Get the rules of the current instance based on the operation
	 * @param $operation
	 * @return array
	 */
	public function getRules($operation) : array
	{
		$result = [];
		$base = $this->rules ?? [];
		switch (Str::lower($operation)) {
			case self::$CREATED:
				$current = $this->createRules ?? [];
				break;
			case self::$UPDATED:
				$current = $this->updateRules ?? [];
				break;
			case self::$DELETED:
				$current = $this->deleteRules ?? [];
				break;
			default:
				$current = [];
				break;
		}
		foreach (array_merge($current, $base) as $name => $nil) {
			$rules = as_array($current[$name] ?? [], "|");
			$baseRules = as_array($base[$name] ?? [], "|");
			$result[$name] = $this->callMutateRule(
				$name,
				array_merge_recursive_distinct($baseRules, $rules),
				$operation
			);
		}
		return $result;
	}
	/**
	 * Check and calls a mutate rule if present.
	 * @param $name
	 * @param $rules
	 * @param $operation
	 * @return mixed
	 */
	private function callMutateRule($name, $rules, $operation)
	{
		if (method_exists($this, 'get'.Str::studly($name).'AttributeRules')) {
			return $this->{'get'.Str::studly($name).'AttributeRules'}($rules, $operation);
		}
		return $rules;
	}
	/**
	 * Return current validation Labels
	 * @return array
	 */
	public function getLabels() : array
	{
		return $this->labels ?? [];
	}
	/**
	 * Return current validation Messages
	 * @return array
	 */
	public function getMessages() : array
	{
		return $this->messages ?? [];
	}
	/**
	 * Return current validation data to perform the validations
	 * @return array
	 */
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
	/**
	 * Return the validator instance.
	 * @param $operation
	 * @return ValidatorContract
	 */
	public function getValidator($operation)
	{
		return Validator::make($this->getValidationData(), $this->getRules($operation), $this->getMessages(), $this->getLabels());
	}
	/**
	 * Validate current instance in the designed operation (default "Create")
	 * @param string $operation
	 */
	public function validate($operation)
	{
		$this->beforeValidate();
		$v = $this->getValidator($operation);
		$v->after(function () use ($v) {
			$this->errors = $v->errors();
		});
		$v->validate();
		$this->afterValidated($v);
	}
	/**
	 * Hooks before the validation happens.
	 */
	public function beforeValidate()
	{
	}
	/**
	 * Hook after the validation happens.
	 * @param ValidatorContract $validator
	 */
	public function afterValidated(ValidatorContract $validator)
	{
	}
}
