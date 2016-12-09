<?php
namespace Tea\Regex;


/**
 * Creates a new Expression object from the given pattern and modifiers.
 * The given pattern will be parsed to extract any modifiers, start of subject/line
 * assertions (\A or ^) and end of subject/line assertions (\z or $).
 *
 * @param  string|null   $pattern
 * @param  string        $modifiers
 * @return \Tea\Regex\Regex
 */
function re($pattern = null, $modifiers = null)
{
	return new Regex($str, $encoding);
}


/**
 * Determine whether the mbstring module is loaded. If strict is false (the default),
 * checks whether a polyfill for mbstring exists.
 *
 * @param  bool   $strict
 * @return bool
 */
function mbstring_loaded($strict = false)
{
	static $extension, $polyfill;

	if(is_null($extension))
		$extension = extension_loaded('mbstring');

	if(is_null($polyfill))
		$polyfill = function_exists('mb_strlen');

	return ($extension || (!$strict && $polyfill));
}


/**
 * Determine whether a value can be casted to string. Returns true if value is a
 * scalar (String, Integer, Float, Boolean etc.), null or if it's an object that
 * implements the __toString() method. Otherwise, returns false.
 *
 * @param  mixed   $value
 * @return bool
 */
function is_stringable($value)
{
	return is_null($value) || is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
}

/**
 * Determine whether a value is iterable and not a string.
 *
 * @param  mixed   $value
 * @return bool
 */
function is_none_string_iterable($value)
{
	return is_iterable($value) && !is_stringable($value);
}

