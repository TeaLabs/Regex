<?php
namespace Tea\Regex;

/**
*
*/
class Regex
{
	const DEFAULT_DELIMITER = '~';
	const DEFAULT_MODIFIERS = 'u';

	protected static $delimiter;

	protected static $modifiers;

	/**
	 * Return all entries in input that match the pattern. If offset and/or length
	 * is passed, the found results will be sliced accordingly using array_slice()
	 *
	 * @uses preg_grep()
	 * @uses array_slice()
	 *
	 * @param  string $pattern
	 * @param  mixed  $input
	 * @param  int    $flags
	 * @param  int    $offset
	 * @param  int    $length
	 * @return array
	*/
	public static function all($pattern, $input, $flags =0, $offset = 0, $length = null)
	{
		$matched = preg_grep(static::addModifiers($pattern), $input, $flags);
		return $offset == 0 && is_null($length)
				? $matched : array_slice($matched, $offset, $length, true);
	}

	/**
	 * Get/set the default regex delimiter. Defaults to Regex::DEFAULT_DELIMITER
	 * if not already set.
	 *
	 * @param  null|string $delimiter
	 * @return string
	 */
	public static function delimiter($delimiter = null)
	{
		if(!is_null($delimiter))
			static::$delimiter = $delimiter;

		return isset(static::$delimiter) ? static::$delimiter : self::DEFAULT_DELIMITER;
	}


	/**
	 * Perform a regular expression search and replace. Identical to
	 * Regex::replace() except it only returns the (possibly transformed)
	 * subjects where there was a match.
	 * If no matches are found or an error occurred, an empty array is returned
	 * when subject is an array or NULL otherwise.
	 *
	 * @uses preg_filter()
	 * @see Regex::replace()
	 *
	 * @param  mixed $pattern
	 * @param  mixed $replacement
	 * @param  mixed $subject
	 * @param  int $limit
	 * @param  int &$count
	 * @return mixed
	*/
	public static function filter($pattern, $replacement, $subject, $limit = -1, &$count = null)
	{
		return preg_filter(static::addModifiers($pattern), $replacement, $subject, $limit, $count);
	}

	/**
	 * Return the first entry in input that match the pattern or null if none.
	 *
	 * @uses preg_grep()
	 *
	 * @param  string $pattern
	 * @param  mixed $input
	 * @param  int $flags
	 * @return string|null
	*/
	public static function first($pattern, $input, $flags = 0)
	{
		$matched = preg_grep(static::addModifiers($pattern), $input, $flags);
		return !empty($matched) ? current($matched) : null;
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @uses preg_match()
	 *
	 * @param  string $pattern
	 * @param  string $subject
	 * @param  int $flags
	 * @param  int $offset
	 * @return bool
	*/
	public static function is($pattern, $subject, $flags =0, $offset = 0)
	{
		return (bool) preg_match(static::addModifiers($pattern), $subject, null, $flags, $offset);
	}

	/**
	 * Return the last entry in input that match the pattern or null if none.
	 *
	 * @uses preg_grep()
	 *
	 * @param  string $pattern
	 * @param  mixed $input
	 * @param  int $flags
	 * @return string|null
	*/
	public static function last($pattern, $input, $flags = 0)
	{
		$matched = preg_grep(static::addModifiers($pattern), $input, $flags);
		return !empty($matched) ? end($matched) : null;
	}

	/**
	 * Perform a regular expression match and return the first occurrences of
	 * $pattern in string. Returns an array containing results of the match if
	 * successful and null on error.
	 *
	 * @uses preg_match()
	 *
	 * @param  string $pattern
	 * @param  string $subject
	 * @param  int $flags
	 * @param  int $offset
	 * @return array|null
	*/
	public static function match($pattern, $subject, $flags =0, $offset = 0)
	{
		$matched = [];
		if(preg_match(static::addModifiers($pattern), $subject, $matched, $flags, $offset) !== false)
			return $matched;
		return null;
	}

	/**
	 * Get/set the default modifiers. Defaults to Regex::DEFAULT_MODIFIERS
	 * if not already set.
	 *
	 * @param  null|string $modifiers
	 * @return string
	 */
	public static function modifiers($modifiers = null)
	{
		if(!is_null($modifiers))
			static::$modifiers = $modifiers;

		return isset(static::$modifiers) ? static::$modifiers : self::DEFAULT_MODIFIERS;
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
	 * @param  string $string                The pattern to quote.
	 * @param  null|string|false $delimiter  Delimiter used in string.
	 * @return string   The quoted string
	*/
	public static function quote($string, $delimiter = null)
	{
		if(is_null($string) || $string == '')
			return $string;

		if(is_null($delimiter))
			$delimiter = static::delimiter();
		elseif($delimiter === false)
			$delimiter = null;

		return preg_quote($string, $delimiter);
	}

	/**
	 * Perform a regular expression search and replace. Searches subject for matches
	 * to pattern and replaces them with replacement.
	 *
	 * @uses preg_replace()
	 *
	 * @param  mixed  $pattern
	 * @param  mixed  $replacement
	 * @param  mixed  $subject
	 * @param  int    $limit
	 * @param  int    $count
	 * @return string|array|null
	 */
	public static function replace($pattern, $replacement, $subject, $limit = -1, &$count = null)
	{
		return preg_replace(static::addModifiers($pattern), $replacement, $subject, $limit, $count);
	}

	/**
	 * Perform a regular expression search and replace using a callback to get
	 * replacements. The behavior of this method is almost identical to
	 * Regex::replace(), except for the fact that instead of replacement parameter,
	 * one should specify a callback.
	 *
	 * @uses preg_replace_callback()
	 *
	 * @param  mixed     $pattern
	 * @param  callable  $callback
	 * @param  mixed     $subject
	 * @param  int       $limit
	 * @param  int       $count
	 * @return string|array|null
	 */
	public static function replaceCallback($pattern, callable $callback, $subject, $limit = -1, &$count = null)
	{
		return preg_replace_callback(static::addModifiers($pattern), $callback, $subject, $limit, $count);
	}

	/**
	 * Perform a regular expression search and replace using a callbacks to get
	 * replacements. The behavior of this method is almost identical to
	 * Regex::replaceCallback(), except for the fact that callbacks are executed
	 * on per-pattern basis.
	 * Each pattern should have a corresponding callback passed as an associative
	 * array mapping patterns (keys) to callbacks (values).
	 *
	 * @uses preg_replace_callback_array()
	 *
	 * @param  array     $pattern
	 * @param  mixed     $subject
	 * @param  int       $limit
	 * @param  int       $count
	 * @return string|array|null
	 */
	public static function replaceCallbackArray(array $patternCallbacks, $subject, $limit = -1, &$count = null)
	{
		$patterns = [];
		foreach ($patternCallbacks as $pattern => $callback)
			$patterns[static::addModifiers($pattern)] = $callback;

		return preg_replace_callback_array($patterns, $subject, $limit, $count);
	}


	/**
	 * Perform a global regular expression search and return all matching results.
	 * Returns a multi-dimensional array containing all matching results of the match if
	 * successful and null on error.
	 *
	 * @uses preg_match_all()
	 *
	 * @param  string $pattern
	 * @param  string $subject
	 * @param  int $flags
	 * @param  int $offset
	 * @return array|null
	*/
	public static function search($pattern, $subject, $flags =0, $offset = 0)
	{
		$matched = [];
		if(preg_match_all(static::addModifiers($pattern), $subject, $matched, $flags, $offset) !== false)
			return $matched;
		return null;
	}

	/**
	 * Split string using a regular expression. Returns an array containing substrings
	 * of subject split along boundaries matched by pattern, or NULL on failure.
	 *
	 * @uses preg_split()
	 *
	 * @param  string $pattern
	 * @param  string $subject
	 * @param  int $limit
	 * @param  int $flags
	 * @return array|null
	*/
	public static function split($pattern, $subject, $limit=-1, $flags =0)
	{
		$result = preg_split(static::addModifiers($pattern), $subject, $limit, $flags);
		return $result !== false ? $result : null;
	}

	/**
	 * Safely remove the given delimiter from the regex pattern if any.
	 * If a delimiter is not provided, removes any possible regex delimiters.
	 * Ie: '/', '#', '~', '+' and '%' or '[]', '{}', '()' and '<>' if bracketStyle
	 * is true.
	 * If a modifiers variable is provided, it will be filled with the modifiers
	 * in the pattern or empty string if none is set.
	 *
	 * @param  string  $regex      The regex pattern
	 * @param  string  $delimiter  The delimiter. Defaults to '/'
	 * @return string
	 */
	public static function unwrap($regex, $delimiter = null, $bracketStyle = false, &$modifiers = null)
	{
		throw new \BadMethodCallException("Method ".__METHOD__." is not implemented.");


		$delimiters = '/#~+%';

		if(!$regex || strpos($delimiters, $regex[0]) !== false)
			return $regex;

		if( strpos('({[<', $regex[0]) !== false){
			if( $has_mbstring === null )
				$has_mbstring = function_exists('mb_substr');
			$end = $has_mbstring
					? mb_substr(rtrim($regex, 'uimsxeADSUXJ'), -1)
					: substr(rtrim($regex, 'uimsxeADSUXJ'), -1);
			if(strpos(')}]>', $end) !== false)
				return $regex;
		}

		return $delimiter.$regex.$delimiter.$modifiers;
	}


	/**
	 * Safely wrap the given regex pattern(s) with the a delimiter and add modifiers
	 * if none is set.
	 *
	 * To add bracket style delimiters, pass a delimiter with both the opening
	 * and closing characters eg. '<>', '{}'. For normal ones use a single
	 * character eg: /','#','%'.
	 *
	 * If $bracketStyle is provided and is true, will check for bracket style
	 * delimiters ie: '[]', '{}', '()', '<>'. Use this when the wrapping a
	 * pattern that might contain bracket style delimiters. But use with care
	 * as the pattern might contain non-quoted brackets.
	 * To be safe, you can provide the bracket delimiter(s) that should be checked
	 * as an array. This will avoid checking through all possible bracket style
	 * delimiters. Eg: if $bracketStyle = ['{<', '}>'], will only check for '{}' and
	 * '<>' delimiters.
	 *
	 * @param  string|array $regex         The regex pattern(s)
	 * @param  string       $delimiter     The delimiter. Defaults to '/'
	 * @param  string       $modifiers     The modifiers. Defaults to 'u'
	 * @param  bool|array   $bracketStyle  Whether to check for bracket delimiters. Defaults to false
	 * @return string|array
	 */
	public static function wrap($regex, $delimiter = null, $modifiers = null, $bracketStyle = false)
	{
		if(is_array($regex)){
			$wrapped = [];

			foreach ($regex as $r)
				$wrapped[] = static::wrap($r, $delimiter, $modifiers, $bracketStyle);

			return $wrapped;
		}

		$regex_0 = mb_substr($regex, 0, 1);
		if(!$regex || strpos('/#~+%', $regex_0) !== false)
			return $regex;

		if($bracketStyle){
			$brackets = is_array($bracketStyle) ? $bracketStyle : ['<({[', '>)}]'];

			if(strpos($brackets[0], $regex_0) !== false)
				if(strpos($brackets[1], mb_substr(rtrim($regex, 'uimsxeADSUXJ'), -1)) !== false)
					return $regex;
		}

		if(strlen($delimiter) > 1)
			list($start, $end) = str_split($delimiter);
		else
			$start = $end = is_null($delimiter) ? static::DEFAULT_DELIMITER : $delimiter;

		if(is_null($modifiers)) $modifiers = 'u';


		// $regex = str_replace(['\\'.$start, "\\".$end], [$start, $end], $regex);
		// $regex = str_replace([$start, $end], ['\\'.$start, "\\".$end], $regex);

		return $start.$regex.$end.$modifiers;
	}


	/**
	 * Add modifiers to the given pattern(s)
	 *
	 * @param  mixed   $pattern
	 * @param  string  $modifiers
	 * @return string|array
	 */
	protected static function addModifiers($pattern, $modifiers = '')
	{
		$modifiers = static::modifiers().$modifiers;


		if(static::canCastValueToStr($pattern) || !is_iterable($pattern))
			return $pattern.$modifiers;

		$modified = [];
		foreach ($pattern as $key => $value)
			$modified[$key] = $value.$modifiers;

		return $modified;
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
}