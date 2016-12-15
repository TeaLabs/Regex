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
	return Helpers::re($pattern, $modifiers);
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
	return Helpers::mbstringLoaded($strict);
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
	return Helpers::isStringable($value);
}

/**
 * Determine whether a value is iterable and not a string.
 *
 * @param  mixed   $value
 * @return bool
 */
function is_none_string_iterable($value)
{
	return Helpers::isNoneStringIterable($value);
}

