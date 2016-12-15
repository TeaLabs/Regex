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
		if($delimiter && static::isValidDelimiter($delimiter, true))
			return static::$delimiter = $delimiter;

		return static::$delimiter;
	}

	/**
	 * Determine if the given value is a valid delimiter. If throwException is
	 * passed and is TRUE, an InvalidDelimiterException will be thrown if delimiter
	 * invalid.
	 *
	 * @param  mixed $value
	 * @param  bool  $orException
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\InvalidDelimiterException
	 */
	public static function isValidDelimiter($value, $orException = false)
	{
		if(strpos(static::ALLOWED_DELIMITERS, (string) $value) !== false)
			return true;

		if(!$orException)
			return false;

		throw new InvalidDelimiterException($value);
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
		if(!is_null($modifiers) && Modifiers::isValid($modifiers, true))
			return static::$modifiers = $modifiers;

		return static::$modifiers;
	}
}