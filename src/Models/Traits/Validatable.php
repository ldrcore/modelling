<?php

namespace LDRCore\Modelling\Models\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

trait Validatable
{
	protected $rules = [];
	
	protected $labels = [];
	
	protected $messages = [];
	
	public static function bootValidatable()
	{
		static::saving(function (self $model) {
			$model->validate();
		});
		static::updating(function (self $model) {
			$model->validate();
		});
		static::deleting(function (self $model) {
			$model->validate();
		});
	}
	
	public function getRules() : array
	{
		return $this->rules;
	}
	
	public function getLabels() : array
	{
		return $this->labels;
	}
	
	public function getMessages() : array
	{
		return $this->messages;
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