<?php
namespace Tea\Regex;

use Closure;
use ArrayAccess;
use InvalidArgumentException;

class Helpers
{
	/**
	 * Creates a new Expression object from the given pattern and modifiers.
	 * The given pattern will be parsed to extract any modifiers, start of subject/line
	 * assertions (\A or ^) and end of subject/line assertions (\z or $).
	 *
	 * @param  string|null   $pattern
	 * @param  string        $modifiers
	 * @return \Tea\Regex\Regex
	 */
	public static function re($pattern = null, $modifiers = null)
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
	public static function mbstringLoaded($strict = false)
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
	public static function isStringable($value)
	{
		return is_string($value)
				|| is_null($value)
				|| is_scalar($value)
				|| (is_object($value) && method_exists($value, '__toString'));
	}

	/**
	 * Determine whether a value is iterable and not a string.
	 *
	 * @param  mixed   $value
	 * @return bool
	 */
	public static function isNoneStringIterable($value)
	{
		return is_iterable($value) && !static::isStringable($value);
	}

	public static function implodeIterable($iterable, $withKeys = true, $glue = null, $prefix = '[', $suffix = ']')
	{
		$results = [];
		foreach ($iterable as $key => $value) {
			$value = static::isNoneStringIterable($value)
					? static::implodeIterable($value, $withKeys, $glue, $prefix, $suffix)
					: (string) $value;
			$results[] = $withKeys ? "{$key} => {$value}" : $value;
		}

		if(is_null($glue)) $glue = ', ';
		return $prefix.join($glue, $results).$suffix;
	}

	public static function iterableToArray($iterable)
	{
		if(is_array($iterable))
			return $iterable;

		if(!is_iterable($iterable)){
			$type = is_object($iterable) ? get_class($iterable) : gettype($iterable);
			throw new InvalidArgumentException("Iterable expected. {$type} given.");
			return;
		}

		$results = [];
		foreach ($iterable as $key => $value) {
			$results[$key] = $value;
		}
		return $results;
	}

	public static function toArray($object)
	{
		if(is_iterable($object))
			return static::iterableToArray($object);

		return (array) $object;
	}

	public static function isArrayAccessible($object)
	{
		return is_array($object) || $object instanceof ArrayAccess;
	}

	public static function value($object)
	{
		return $object instanceof Closure ? $object() : $object;
	}
}

