<?php
namespace Tea\Regex;

/**
*
*/
class Regex
{



	/**
	 * Create a new Builder instance. Accepts an optional pattern from which
	 * the builder can be created. The pattern can be another Builder instance
	 * or a raw regex string.
	 * Throws a InvalidRegexPatternException if the given pattern is not a Builder
	 * instance and can't be converted to string.
	 *
	 * @see   \Tea\Regex\Builder::build()
	 * @uses  \Tea\Regex\Builder::build()
	 * @param  string|\Tea\Regex\Builder|null   $pattern
	 * @return \Tea\Regex\Builder
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public static function build($pattern = null)
	{
		return Builder::build($pattern);
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
			$delimiter = Config::delimiter();
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


}