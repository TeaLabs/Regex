<?php
namespace Tea\Regex;

/**
*
*/
class Regex
{

	/**
	 * @var string
	 */
	protected $pattern;


	/**
	 * Instantiate the Regex instance.
	 *
	 * @param  string   $pattern
	 * @return void
	 */
	public function __construct($pattern)
	{
		$this->pattern = $pattern;
	}

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
	 * @param  string|null                      $modifiers
	 * @return \Tea\Regex\Builder
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public static function build($pattern = null, $modifiers = null)
	{
		return Builder::build($pattern, $modifiers);
	}

	/**
	 * @param  string  $pattern
	 * @param  string|null  $modifiers
	 * @return \Tea\Regex\RegularExpression
	 */
	public static function create($pattern, $modifiers = null)
	{
		return new RegularExpression($pattern, $modifiers);
	}

	/**
	 * @param  string  $pattern
	 * @param  string|null  $modifiers
	 * @return \Tea\Regex\RegularExpression
	 */
	public static function compile($pattern, $modifiers = null)
	{
		if($pattern instanceof RegularExpression)
			return $pattern;
		elseif ($pattern instanceof Builder)
			return $modifiers ? $pattern->modifiers($modifiers)->compile() : $pattern->compile();
		elseif(is_stringable($pattern))
			return static::build($pattern, $modifiers)->compile();
		elseif(is_iterable($pattern))
			return static::compileAll($pattern, $modifiers);
		else
			return static::build($pattern, $modifiers)->compile();
	}

	/**
	 * @param  iterable  $pattern
	 * @param  string|null  $modifiers
	 * @return array
	 */
	public static function compileAll($patterns, $modifiers = null)
	{
		$compiled = [];
		foreach ($patterns as $pattern)
			$compiled[] = static::compile($pattern);

		return $compiled;
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @uses preg_match()
	 *
	 * @param  string $pattern
	 * @param  mixed $subject
	 * @param  int $flags
	 * @param  int $offset
	 * @return bool
	 */
	public static function is($pattern, $subject, $flags =0, $offset = 0)
	{
		$matches = null;
		return (bool) preg_match(static::addModifiers($pattern), $subject, $matches, $flags, $offset);
	}


	/**
     * @param Tea\Regex\RegularExpression|Tea\Regex\Bulder|string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\MatchResult
	 */
	public static function match($pattern, $subject, $flags = 0, $offset = 0)
	{
		return static::compile($pattern)->match($subject, $flags, $offset);
	}

	/**
     * @param Tea\Regex\RegularExpression|Tea\Regex\Bulder|string $pattern
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\MatchAllResult
	 */
	public static function matchAll($pattern, $subject, $flags = 0, $offset = 0)
	{
		return MatchAllResult::for(static::compile($pattern), $subject, $flags, $offset);
	}

	/**
     * @param Tea\Regex\RegularExpression|Tea\Regex\Bulder|string $pattern
	 * @param string|callable $replacement
	 * @param string          $subject
	 * @param int             $limit
	 *
	 * @return \Tea\Regex\ReplaceResult
	 */
	public static function replace($pattern, $replacement, $subject, $limit = -1)
	{
		return ReplaceResult::for(static::compile($pattern), $replacement, $subject, $limit);
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