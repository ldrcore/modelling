<?php

namespace LDRCore\Modelling\Models;

/**
 * Class Rules
 * Contains the validation rules to be used on a Validator class
 * @package LDRCore\Modelling\Models
 */
abstract class Rules
{
	/**
	 * Set of default rules to be applied in all operations
	 * @return array
	 */
	public function rules() : array
	{
		return [];
	}
	/**
	 * Set of rules to be applied only when creating the instance
	 * @return array
	 */
	public function create() : array
	{
		return [];
	}
	/**
	 * Set of rules to be applied only when updating the instance
	 * @return array
	 */
	public function update() : array
	{
		return [];
	}
	/**
	 * Set of rules to be applied when deleting the instance
	 * @return array
	 */
	public function delete() : array
	{
		return [];
	}
	/**
	 * Validation labels
	 * @return array
	 */
	public function labels() : array
	{
		return [];
	}
	/**
	 * Validation messages
	 * @return array
	 */
	public function messages() : array
	{
		return [];
	}
}
