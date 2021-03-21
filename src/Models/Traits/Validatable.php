<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use LDRCore\Modelling\Models\Observers\ValidatableObserver;
use LDRCore\Modelling\Models\Rules;

/**
 * Trait Validatable
 * @property array|Rules $rules public Set of default rules to be applied in all operations
 * @property array $createRules public Set of rules to be applied only when creating the instance
 * @property array $updateRules public Set of rules to be applied only when updating the instance
 * @property array $deleteRules public Set of rules to be applied only when deleting the instance
 * @package LDRCore\Modelling\Models\Traits
 */
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
		$obj = $this->getBaseRules();
		$base = $obj instanceof Rules ? $obj->rules() : ($this->rules ?? []);
		switch (Str::lower($operation)) {
			case self::$CREATED:
				$current = $obj instanceof Rules ? $obj->create() : ($this->createRules ?? []);
				break;
			case self::$UPDATED:
				$current = $obj instanceof Rules ? $obj->update() : ($this->updateRules ?? []);
				break;
			case self::$DELETED:
				$current = $obj instanceof Rules ? $obj->delete() : ($this->deleteRules ?? []);
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
	 * Determine if current rules will be handled in the model or a separate class
	 * @return array|Rules
	 */
	private function getBaseRules()
	{
		if (!is_array($this->rules)){
			try {
				$obj = Container::getInstance()->make($this->rules, []);
				if ($obj instanceof Rules) {
					return $obj;
				}
			} catch (\Exception $e) {
				// Do nothing
			}
		}
		return $this->rules;
	}
	/**
	 * Return current validation Labels
	 * @return array
	 */
	public function getLabels() : array
	{
		$obj = $this->getBaseRules();
		$base = $obj instanceof Rules ? $obj->labels() : [];
		$labels = $this->labels ?? [];
		return array_merge_recursive_distinct($base, $labels);
	}
	/**
	 * Return current validation Messages
	 * @return array
	 */
	public function getMessages() : array
	{
		$obj = $this->getBaseRules();
		$base = $obj instanceof Rules ? $obj->messages() : [];
		$messages = $this->messages ?? [];
		return array_merge_recursive_distinct($base, $messages);
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
     * @param $data
     * @param $rules
	 * @return ValidatorContract
	 */
	public function makeValidator($data, $rules)
	{
		return Validator::make($data, $rules, $this->getMessages(), $this->getLabels());
	}
	/**
	 * Validate current instance in the designed operation (default "Create")
	 * @param string $operation
	 */
	public function validate($operation)
	{
		$this->beforeValidate();
		$v = $this->makeValidator($this->getValidationData(), $this->getRules($operation), $operation);
		$v->after(function () use ($v) {
			$this->errors = $v->errors();
		});
		$v->validate();
		$this->afterValidated($v);
	}
    /**
     * Validate the mass operation using current defined rules
     * @param array $values
     * @param $operation
     */
	public function massValidate(array $values, $operation)
    {
        $this->beforeMassValidate();
        $fields = $this->getRules($operation);
        $uniques = $this->getUniqueRules($fields);
        $labels = $this->getLabels();
        $newRules = [];
        foreach ($fields as $field => $rules) {
            $newRules["*.{$field}"] = $rules;
        }
        $v = $this->makeValidator($values, $newRules);
        $v->after(function ($v) use ($values, $uniques) {
            $values = collect($values);
            foreach ($uniques as $field) {
                $temp = collect([]);
                $list = $values->pluck($field);
                $attribute = $labels[$field] ?? $field;
                foreach ($list as $index => $value) {
                    if ($temp->contains($value)) {
                        $v->errors()->add("$index.$field", __('validation.unique', ['attribute' => "$index.$attribute"]));
                    } else {
                        $temp->add($value);
                    }
                }
                // Cleaning
                unset($temp);
                unset($list);
            }
        });
        $v->validate();
        $this->afterMassValidated($v);
    }
    /**
     * List defined unique rules
     * @param $fields
     * @return array
     */
	public function getUniqueRules($fields)
    {
        $uniques = [];
        foreach ($fields as $field => $rules) {
            foreach ($rules as $rule) {
                if ( $rule instanceof Unique || (is_string($rule) && stripos($rule, 'unique')!==false) ) {
                    $uniques[] = $field;
                }
            }
        }
        return $uniques;
    }
	/**
	 * Hooks before the validation happens.
	 */
	public function beforeValidate()
	{
	}
	/**
	 * Hooks before the mass validation happens.
	 */
	public function beforeMassValidate()
	{
	}
	/**
	 * Hook after the validation happens.
	 * @param ValidatorContract $validator
	 */
	public function afterValidated(ValidatorContract $validator)
	{
	}
	/**
	 * Hook after the mass validation happens.
	 * @param ValidatorContract $validator
	 */
	public function afterMassValidated(ValidatorContract $validator)
	{
	}
}
