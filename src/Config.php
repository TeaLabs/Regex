<?php
namespace Tea\Regex;


use Tea\Regex\Exception\InvalidDelimiterException;

class Config
{
	const ALLOWED_DELIMITERS = '/~#%';

	/**
	 * The global default delimiter set on runtime.
	 *
	 * @var string
	 */
	protected static $delimiter = '/';

	/**
	 * The global default modifiers set on runtime.
	 *
	 * @var string
	 */
	protected static $modifiers = 'u';

	/**
	 * Get/set the default regex delimiter. If a valid delimiter is passed,
	 * it will be used as the default from here on.
	 * If a default value has not yet been set, Tea\Regex\Defaults::DELIMITER
	 * is used as the default
	 *
	 * @param  null|string $delimiter
	 * @return string
	 *
	 * @throws \Tea\Regex\Exception\InvalidDelimiterException
	 */
	public static function delimiter($delimiter = null)
	{
		if($delimiter && static::validateDelimiter($delimiter))
			return static::$delimiter = $delimiter;

		return static::$delimiter;
	}

	/**
	 * Determine if the given value is a valid delimiter.
	 *
	 * @see  \Tea\Regex\Config::ALLOWED_DELIMITERS for a list of valid delimiters.
	 * @uses \Tea\Regex\Config::validateDelimiter() but does not throw any exception.
	 *
	 * @param  mixed $delimiter
	 * @return bool
	 */
	public static function isValidDelimiter($delimiter)
	{
		return static::validateDelimiter($delimiter, true);
	}

	/**
	 * Determine if the given value is a valid delimiter. Unless silent is set to
	 * TRUE, an InvalidDelimiterException will be thrown if the value is invalid.
	 *
	 * @see \Tea\Regex\Config::ALLOWED_DELIMITERS for a list of valid delimiters.
	 *
	 * @param  mixed $delimiter
	 * @param  bool  $silent
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\InvalidDelimiterException
	 */
	public static function validateDelimiter($delimiter, $silent = false)
	{
		if(strpos(self::ALLOWED_DELIMITERS, (string) $delimiter) !== false)
			return true;

		if($silent)	return false;

		throw new InvalidDelimiterException($delimiter);
	}

	/**
	 * Get/set the default modifiers. Defaults to Regex::DEFAULT_MODIFIERS
	 * if not already set.
	 *
	 * @param  null|string $modifiers
	 * @return string
	 *
	 * @throws \Tea\Regex\Exception\InvalidModifierException
	 * @see    \Tea\Regex\Modifiers::isValid()
	 */
	public static function modifiers($modifiers = null)
	{
		if(!is_null($modifiers) && Modifiers::validate($modifiers))
			return static::$modifiers = $modifiers;

		return static::$modifiers;
	}
}