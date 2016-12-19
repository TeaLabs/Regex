<?php
namespace Tea\Regex;

use Closure;
use Exception;
use Tea\Regex\Result\Matches;
use Tea\Regex\Result\Replacement;
use Tea\Regex\Exception\SplitError;
use Tea\Regex\Exception\MatchError;
use Tea\Regex\Exception\FilterError;
use Tea\Regex\Exception\ReplacementError;

/**
*
*/
class Adapter
{
	/**
	 * @var array
	*/
	protected static $pregErrors;

	/**
	 * Filter the given input and return only the entries that match the pattern.
	 * If invert is passed as TRUE, the elements of the input that do not match
	 * the given pattern will be returned.
	 *
	 * @uses preg_grep()
	 *
	 * @param  string  $pattern
	 * @param  array   $input
	 * @param  bool    $invert
	 *
	 * @return array
	 *
	 * @throws \Tea\Regex\Exception\FilterError
	*/
	public static function filter($pattern, array $input, $invert = false)
	{
		try{
			$flags = $invert ? PREG_GREP_INVERT : 0;
			$result = preg_grep($pattern, $input, $flags);
		}
		catch (Exception $exception){
			throw FilterError::create($pattern, $input, $exception->getMessage());
		}

		if (empty($result) && static::lastError(true))
			throw FilterError::create($pattern, $input, static::lastError());

		return $result;
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 * Alias for Adapter::matches()
	 *
	 * @see  \Tea\Regex\Adapter::matches()
	 * @todo Add support of PREG_* flags and how they affect the results' nature.
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public static function is($pattern, $subject, $flags =0, $offset = 0)
	{
		return static::match($pattern, $subject, $flags, $offset)->any();
	}


	/**
	 * Perform a regular expression match on given subject.
	 *
	 * @todo Add support of PREG_* flags and how they affect the results' nature.
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\Result\Matches
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public static function match($pattern, $subject, $flags = 0, $offset = 0)
	{
		try{
			$result = preg_match($pattern, $subject, $matches, $flags, $offset);
		}
		catch (Exception $exception){
			throw MatchError::create($pattern, $subject, $exception->getMessage());
		}

		if ($result === false)
			throw MatchError::create($pattern, $subject, static::lastError());

		return new Matches($pattern, $subject, $matches, $result);
	}

	/**
	 * Perform a global regular expression match on given subject.
	 *
	 * @todo Add support of PREG_* flags and how they affect the results' nature.
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\Result\Matches
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public static function matchAll($pattern, $subject, $flags = 0, $offset = 0)
	{
		try{
			$result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
		}
		catch (Exception $exception){
			throw MatchError::create($pattern, $subject, $exception->getMessage());
		}

		if ($result === false){
			throw MatchError::create($pattern, $subject, static::lastError());
		}

		return new Matches($pattern, $subject, $matches, $result, true);
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @todo Add support of PREG_* flags and how they affect the results' nature.
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public static function matches($pattern, $subject, $flags =0, $offset = 0)
	{
		return static::match($pattern, $subject, $flags, $offset)->any();
	}

	/**
	 * Quote (escape) regular expression characters and the delimiter in string.
	 * If not passed, the default delimiter {@see Tea\Regex\Config::delimiter()}
	 * will be quoted. FALSE can be passed as the delimiter to prevent any
	 * delimiter including the default from being quoted.
	 *
	 * @uses preg_quote()
	 *
	 * @param  string            $value
	 * @param  string|null|false $delimiter
	 * @return string
	*/
	public static function quote($value, $delimiter = null)
	{
		if(empty($value))
			return $value;

		$delimiter = $delimiter === false ? null : ($delimiter ?: Config::delimiter());

		if(Helpers::isNoneStringIterable($value)){
			$results = [];
			foreach ($value as $k => $v) {
				$results[$k] = preg_quote( (string) $v, $delimiter);
			}
			return $results;
		}

		return preg_quote( (string) $value, $delimiter);
	}


	/**
	 * Perform a regular expression search and replace
	 *
	 * @param string|array           $pattern
	 * @param string|array|\Closure  $replacement
	 * @param string|array           $subject
	 * @param int                    $limit
	 *
	 * @return \Tea\Regex\Result\Replacement
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	 */
	public static function replace($pattern, $replacement, $subject, $limit = -1)
	{
		if($replacement instanceof Closure){
			return static::replaceCallback($pattern, $replacement, $subject, $limit);
		}

		try {
			$replaced = preg_replace($pattern, $replacement, $subject, $limit, $count);
		}
		catch (Exception $exception) {
			throw ReplacementError::create($pattern, $subject, $exception->getMessage());
		}

		if ($replaced === null)
			throw ReplacementError::create($pattern, $subject, static::lastError());

		return new Replacement($pattern, $subject, $replacement, $replaced, $count, $limit);
	}

	/**
	 * Perform a regular expression search and replace using a callback.
	 *
	 * @param string|array    $pattern
	 * @param callable        $callback
	 * @param string|array    $subject
	 * @param int             $limit
	 *
	 * @return \Tea\Regex\Result\Replacement
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	 */
	public static function replaceCallback($pattern, callable $callback, $subject, $limit = -1)
	{
		$replacement = function (array $matches) use ($pattern, $subject, $callback) {
			return $callback(new Matches($pattern, $subject, $matches, true));
		};

		try {
			$replaced = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);
		}
		catch (Exception $exception) {
			throw ReplacementError::create($pattern, $subject, $exception->getMessage());
		}

		if ($replaced === null)
			throw ReplacementError::create($pattern, $subject, static::lastError());

		return new Replacement($pattern, $subject, $callback, $replaced, $count, $limit);
	}


	/**
	 * Perform a regex search and replace. Identical to Adapter::replace()
	 * except it only returns the (possibly transformed) subjects where there
	 * was a match. Returns NULL if no matches are found regardless of whether
	 * the subject was a string or array.
	 *
	 * @uses  preg_filter()
	 *
	 * @param string|array           $pattern
	 * @param string|array           $replacement
	 * @param string|array           $subject
	 * @param int                    $limit
	 *
	 * @return \Tea\Regex\Result\Replacement|null
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	*/
	public static function replaced($pattern, $replacement, $subject, $limit = -1)
	{
		try {
			$replaced = preg_filter($pattern, $replacement, $subject, $limit, $count);
		}
		catch (Exception $exception) {
			throw ReplacementError::create($pattern, $subject, $exception->getMessage());
		}

		if ((is_null($replaced) || $replaced == []) && static::lastError(true)){
			throw ReplacementError::create($pattern, $subject, static::lastError());
		}

		return $count === 0 ? null
			: new Replacement($pattern, $subject, $replacement, $replaced, $count, $limit);
	}


	/**
	 * Split string using a regular expression. Returns an array containing
	 * substrings of subject split along boundaries matched by pattern.
	 *
	 * @uses preg_split()
	 *
	 * @param  string $pattern
	 * @param  string $subject
	 * @param  int $limit
	 * @param  int $flags
	 * @return array
	 *
	 * @throws \Tea\Regex\Exception\SplitError
	*/
	public static function split($pattern, $subject, $limit=-1, $flags =0)
	{
		try {
			$result = preg_split($pattern, $subject, $limit, $flags);
		}
		catch (Exception $exception) {
			throw SplitError::create($pattern, $subject, $exception->getMessage());
		}

		if (static::lastError(true) && count($result) === 1 && $result[0] == $subject){
			throw SplitError::create($pattern, $subject, static::lastError());
		}

		return $result;
	}


	public static function lastError($code = false)
	{
		if(is_null(static::$pregErrors)){
			$constants = get_defined_constants(true);
			static::$pregErrors = [];
			foreach ($constants['pcre'] as $key => $value){
				if(substr($key, -6) === '_ERROR')
					static::$pregErrors[$value] = $key;
			}
		}
		return $code ? preg_last_error() : static::$pregErrors[preg_last_error()];
	}

	/**
	 * Parse a regex pattern and return its components.
	 *
	 * @todo   Add flags to provide options on what will be extracted.
	 *
	 * @param  string  $pattern
	 *
	 * @return \Tea\Regex\Result\Matches
	 */
	public static function parsePattern($pattern)
	{
		static $parser = '/ ^ (?P<delimiter> [\/\~\#\%\+]{0,1})  (?P<body> .+) \1 (?P<modifiers> [uimsxADSUXJ\s]*) $ /uxs';

		return static::match($parser, trim($pattern, " "));
	}

}