<?php
namespace Tea\Regex;

/**
*
*/
class Regex extends Builder
{
	const DEFAULT_DELIMITER = '~';
	const DEFAULT_MODIFIERS = 'u';

	/**
	 * String of possible delimiters
	 *
	 * @var string
	 */
	protected static $possibleDelimiters = '/~#%+';

	/**
	 * String of allowed modifiers.
	 *
	 * @var string
	 */
	protected static $allowedModifiers = 'uimsxADSUXJ';

	/**
	 * The global default delimiter set on runtime.
	 *
	 * @var string
	 */
	protected static $defaultDelimiter;

	/**
	 * The global default modifiers set on runtime.
	 *
	 * @var string
	 */
	protected static $defaultModifiers;


	/**
	 * Create a new Regex instance.
	 *
	 * @param  null|string $pattern
	 * @return static
	 */
	public static function build($pattern = null, $modifiers = null)
	{
		if(!is_null($delimiter) && !empty($delimiter))
			return static::$defaultDelimiter = $delimiter;

		return isset(static::$defaultDelimiter) ? static::$defaultDelimiter : self::DEFAULT_DELIMITER;
	}

	protected static function parsePattern($pattern, $flags = 0)
	{

	}


	/**
	 * Get/set the default regex delimiter. Defaults to Regex::DEFAULT_DELIMITER
	 * if not already set.
	 *
	 * @param  null|string $delimiter
	 * @return string
	 */
	public static function defaultDelimiter($delimiter = null)
	{
		if(!is_null($delimiter) && !empty($delimiter))
			return static::$defaultDelimiter = $delimiter;

		return isset(static::$defaultDelimiter) ? static::$defaultDelimiter : self::DEFAULT_DELIMITER;
	}

	/**
	 * Get/set the default modifiers. Defaults to Regex::DEFAULT_MODIFIERS
	 * if not already set.
	 *
	 * @param  null|string $modifiers
	 * @return string
	 */
	public static function defaultModifiers($modifiers = null)
	{
		if(!is_null($modifiers))
			return static::$defaultModifiers = $modifiers;

		return isset(static::$defaultModifiers) ? static::$defaultModifiers : self::DEFAULT_MODIFIERS;
	}

	/**
	 * Quote (escape) regular expression characters and the delimiter in string.
	 * Unless a $delimiter is passed, the default delimiter (Regex::delimiter())
	 * will be quoted. FALSE can be passed as the delimiter to prevent any delimiter
	 * including the default from being quoted.
	 *
	 * @see  Regex::delimiter()
	 * @uses preg_quote()
	 *
	 * @param  string $value                The pattern to quote.
	 * @param  null|string|false $delimiter  Delimiter used in string.
	 * @return string   The quoted string
	*/
	public static function quote($value, $delimiter = null)
	{
		if(is_null($value) || $value == '')
			return $value;

		if(is_null($delimiter))
			$delimiter = static::defaultDelimiter();
		elseif($delimiter === false)
			$delimiter = null;

		if(is_stringable($value) || !is_iterable($value))
			return preg_quote( (string) $value, $delimiter);

		$results = [];
		foreach ($value as $k => $v) {
			$results[$k] = preg_quote($v, $delimiter);
		}
		return $results;
	}


	/**
	 * Determine if the current value can be cast to string.
	 *
	 * @param  mixed   $value
	 * @return bool
	 */
	protected static function canCastValueToStr($value)
	{
		if(is_null($value) || is_scalar($value))
			return true;

		if(is_object($value) && method_exists($value, '__toString'))
			return true;

		return false;
	}


	/**
	 * Get a valid iterable string(s) from the given value.
	 *
	 * @param  mixed   $value
	 * @param  bool    $strict
	 * @param  string  $method
	 * @param  string  $argName
	 * @return array|Traversable
	 * @throws TypeError
	 */
	protected static function strToIterableOrIterable($value, $strict = false, $method = null, $argName = null)
	{
		if(static::canCastValueToStr($value))
			return [$value];

		if(is_iterable($value))
			return $value;

		if(!$strict)
			return (array) $value;

		$method = $method ?: '';
		$argName = $argName ?: '';
		$type = ucfirst(is_object($value) ? get_class($value) : gettype($value));

		throw new \TypeError("Regex method \"{$method}\" argument \"{$argName}\":"
			." Accepts values that can be cast to string (see Tea\Uzi\can_str_cast()),"
			." arrays or Traversable objects. \"{$type}\" given.");
	}

}