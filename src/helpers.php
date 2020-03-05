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