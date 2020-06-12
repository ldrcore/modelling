<?php

/**
 * Determine if an object contains some traint in it or it's parent's uses.
 * @param $object
 * @param string $trait
 * @return bool
 */
if (!function_exists("has_trait")) {
	function has_trait($object, string $trait) : bool
    {
    	static $checked;
    	$current = get_class($object).$trait;
    	if (!isset($checked[$current])) {
	        $uses = class_uses_recursive($object, false);
    		$checked[$current] = array_key_exists($trait, $uses);
	    }
    	return $checked[$current];
    }
}

/**
 * Return the passed value as an array
 * @param $value
 * @param string|null $separator
 * @return array
 */
if (!function_exists('as_array')) {
	function as_array($value, $separator = null) : array
	{
	    if (is_array($value)) {
	        return $value;
	    } elseif ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
	        return $value->toArray();
	    } elseif ($separator !== null) {
	    	return explode($separator, $value);
	    }
	    return [$value];
	}
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
if (!function_exists('array_merge_recursive_distinct')) {
	function array_merge_recursive_distinct(array $array1, array $array2)
	{
	    $merged = $array1;
	    foreach ($array2 as $key => &$value) {
	        if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
	            $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
	        } else {
	            $merged [$key] = $value;
	        }
	    }
	
	    return $merged;
	}
}
