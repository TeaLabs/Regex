<?php
namespace Tea\Regex;

use TypeError;
use Tea\Regex\Utils\Helpers;
use Tea\Contracts\Regex\Pattern;
use Tea\Contracts\Regex\RegularExpression as Contract;
/**
*
*/
class RegularExpression implements Contract
{
	/**
	 * @var string
	 */
	protected $body;

	/**
	 * @var string
	 */
	protected $modifiers;

	/**
	 * @var string
	 */
	protected $delimiter;

	/**
	 * Instantiate the RegularExpression instance.
	 * If either the modifiers and/or the delimiter are not provided, the defaults
	 * {@see \Tea\Regex\Config} will be used.
	 *
	 * @param  string              $body
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 * @return void
	 */
	public function __construct($body, $modifiers = null, $delimiter = null)
	{
		$this->body = $body;
		$this->modifiers = is_null($modifiers) ? Config::modifiers() : $modifiers;
		$this->delimiter = $delimiter ?: Config::delimiter();
	}

	/**
	 * Filter the given input and return only the entries that match the pattern.
	 * If invert is passed as TRUE, the elements of the input that do not match
	 * the given pattern will be returned.
	 *
	 * @see   \Tea\Regex\Adapter::filter()
	 *
	 * @param  array   $input
	 * @param  int     $flags
	 *
	 * @return array
	 *
	 * @throws \Tea\Regex\Exception\FilterError
	*/
	public function filter(array $input, $flags = 0)
	{
		return Adapter::filter($this->compile(), $input, $flags);
	}

	/**
	 * Determine if the given string matches the given regex pattern. Alias for
	 * {@see \Tea\Regex\RegularExpression::matches()}.
	 *
	 * @uses \Tea\Regex\RegularExpression::matches()
	 *
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function is($subject, $flags =0, $offset = 0)
	{
		return $this->matches($subject, $flags, $offset);
	}

	/**
	 * Perform a regular expression match on given subject.
	 *
	 * @see  \Tea\Regex\Adapter::match()
	 *
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\Result\Matches
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function match($subject, $flags = 0, $offset = 0)
	{
		return Adapter::match($this->compile(), $subject, $flags, $offset);
	}

	/**
	 * Perform a global regular expression match on given subject.
	 *
	 * @see  \Tea\Regex\Adapter::matchAll()
	 *
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\Result\MatchResult
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function matchAll($subject, $flags = 0, $offset = 0)
	{
		return Adapter::matchAll($this->compile(), $subject, $flags, $offset);
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @see  \Tea\Regex\Adapter::matches()
	 *
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function matches($subject, $flags =0, $offset = 0)
	{
		return Adapter::matches($this->compile(), $subject, $flags, $offset);
	}

	/**
	 * Perform a regular expression search and replace.
	 *
	 * @see  \Tea\Regex\Adapter::replace()
	 *
	 * @param string|array|\Closure  $replacement
	 * @param string|array           $subject
	 * @param int                    $limit
	 *
	 * @return \Tea\Regex\Result\Replacement
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	 */
	public function replace($replacement, $subject, $limit = -1)
	{
		return Adapter::replace($this->compile(), $replacement, $subject, $limit);
	}

	/**
	 * Perform a regular expression search and replace using a callback.
	 *
	 * @see  \Tea\Regex\Adapter::replaceCallback()
	 *
	 * @param callable        $callback
	 * @param string|array    $subject
	 * @param int             $limit
	 *
	 * @return \Tea\Regex\Result\Replacement
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	 */
	public function replaceCallback(callable $callback, $subject, $limit = -1)
	{
		return Adapter::replaceCallback($this->compile(), $callback, $subject, $limit);
	}

	/**
	 * Perform a regex search and replace. Identical to Adapter::replace()
	 * except it only returns the (possibly transformed) subjects where there
	 * was a match. Returns NULL if no matches are found regardless of whether
	 * the subject was a string or array.
	 *
	 * @see  \Tea\Regex\Adapter::replaced()
	 *
	 * @param string|array           $replacement
	 * @param string|array           $subject
	 * @param int                    $limit
	 *
	 * @return \Tea\Regex\Result\Replacement|null
	 *
	 * @throws \Tea\Regex\Exception\ReplacementError
	*/
	public function replaced($replacement, $subject, $limit = -1)
	{
		return Adapter::replaced($this->compile(), $replacement, $subject, $limit);
	}

	/**
	 * Split string using a regular expression. Returns an array containing
	 * substrings of subject split along boundaries matched by pattern.
	 *
	 * @see  \Tea\Regex\Adapter::split()
	 *
	 * @param  string $subject
	 * @param  int $limit
	 * @param  int $flags
	 * @return array
	 *
	 * @throws \Tea\Regex\Exception\SplitError
	*/
	public function split($subject, $limit=-1, $flags =0)
	{
		return Adapter::split($this->compile(), $subject, $limit, $flags);
	}


	/**
	 * Add the given modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @return void
	 */
	public function addModifiers($modifiers)
	{
		$modifiers = Helpers::isNoneStringIterable($modifiers)
				? $modifiers : str_split((string) $modifiers);
		$new  = '';
		foreach ($modifiers as $modifier) {
			$modifier = trim((string) $modifier);
			if($modifier && !$this->hasModifiers($modifier))
				$new .= $modifier;
		}

		if($new && Modifiers::validate($new))
			$this->modifiers .= $new;


		return $this;
	}


	/**
	 * Determine whether the regex has all or any of the given modifiers.
	 * By default, this method checks if the regex has all the given modifiers.
	 * But accepts an optional $any which if set to TRUE, will return TRUE if
	 * at least one of the given modifiers is set.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @param  bool              $any
	 * @return bool
	 */
	public function hasModifiers($modifiers, $any = false)
	{
		$modifiers = Helpers::isNoneStringIterable($modifiers)
						? $modifiers : str_split((string) $modifiers);

		foreach ($modifiers as $modifier) {
			$has = strpos($this->modifiers, $modifier) !== false;
			if(($any && $has) || (!$any && !$has))
				return $has;
		}
		return $any ? false : true;
	}

	/**
	 * Remove the given modifiers.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @return $this
	 */
	public function removeModifiers($modifiers)
	{
		$modifiers = Helpers::isNoneStringIterable($modifiers)
						? $modifiers : str_split((string) $modifiers);

		$this->modifiers = str_replace($modifiers, '', $this->modifiers);

		return $this;
	}


	/**
	 * Get the regex body.
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Get the regex modifiers.
	 *
	 * @return string
	 */
	public function getModifiers()
	{
		return $this->modifiers;
	}

	/**
	 * Get the regex delimiter.
	 *
	 * @return string
	 */
	public function getDelimiter()
	{
		return $this->delimiter;
	}

	/**
	 * Get the complied value of the RegularExpression. This method returns
	 * $this since it already implements __toString() making it compatible.
	 *
	 * @return static
	 */
	public function compile()
	{
		return $this;
	}

	/**
	 * Cast the Regular Expression object to string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getDelimiter()
				.$this->getBody()
				.$this->getDelimiter()
				.$this->getModifiers();
	}

	/**
	 * Create a RegularExpression instance.
	 * If either the modifiers and/or the delimiter are not provided, the defaults
	 * {@see \Tea\Regex\Config} will be used.
	 *
	 * @param  string              $body
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 * @return static
	 */
	public static function create($body, $modifiers = null, $delimiter = null)
	{
		return new static($body, $modifiers, $delimiter);
	}

	/**
	 * Create a RegularExpression instance from a possibly complete regex string
	 * or a {@see \Tea\Regex\Contracts\Pattern} instance.
	 * If the given pattern is string it will be parsed to extract the regex body,
	 * modifiers and the delimiter if any.
	 *
	 * If either the modifiers and/or delimiter are neither set on the pattern
	 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
	 * Modifiers and/or the delimiter passed as arguments will be used instead
	 * of those set on the pattern
	 *
	 * @uses \Tea\Regex\RegularExpression::fromInstance()
	 * @uses \Tea\Regex\RegularExpression::fromString()
	 *
	 * @param  string|\Tea\Regex\Contracts\Pattern  $pattern
	 * @param  string|null|false                    $modifiers
	 * @param  string|null                          $delimiter
	 *
	 * @return static
	 */
	public static function from($pattern, $modifiers = null, $delimiter = null)
	{
		if($pattern instanceof Pattern)
			return static::fromInstance($pattern, $modifiers, $delimiter);
		elseif(Helpers::isStringable($pattern))
			return static::fromString($pattern, $modifiers, $delimiter);
		elseif(is_iterable($pattern))
			return static::fromCollection($pattern, $modifiers, $delimiter);

		$type = Helpers::type($pattern);

		throw new TypeError("RegularExpression objects can only be created from strings (or objects"
				." implementing the __toString method), instances of Tea\Regex\Contracts\Pattern"
				." or iterables containing objects of any of the former types. {$type} given.");
	}

	/**
	 * Create a RegularExpression instance from a
	 * {@see \Tea\Regex\Contracts\Pattern} instance.
	 * If either the modifiers and/or delimiter are neither set on the pattern
	 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
	 * Modifiers and/or the delimiter passed as arguments will be used instead
	 * of those set on the pattern.
	 *
	 * @param  \Tea\Regex\Contracts\Pattern  $pattern
	 * @param  string|null|false             $modifiers
	 * @param  string|null                   $delimiter
	 *
	 * @return static
	 */
	public static function fromInstance(Pattern $pattern, $modifiers = null, $delimiter = null)
	{
		$modifiers = is_null($modifiers)
			? ($pattern->getModifiers() === null ? false : $pattern->getModifiers()) : $modifiers;
		$delimiter = is_null($delimiter) ? $pattern->getDelimiter() : $delimiter;

		return new static($pattern->getBody(), $modifiers, $delimiter);
	}

	/**
	 * Create a RegularExpression instance from a possibly complete regex string.
	 * The given regex string will be parsed to extract the regex body, modifiers
	 * and the delimiter if any.
	 * If either the modifiers and/or delimiter are neither set on the pattern
	 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
	 * Modifiers and/or the delimiter passed as arguments will be used instead
	 * of those set on the pattern.
	 *
	 * @uses \Tea\Regex\Adapter::parsePattern()
	 *
	 * @param  string              $pattern
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 *
	 * @return static
	 */
	public static function fromString($pattern, $modifiers = null, $delimiter = null)
	{
		$pattern = Adapter::parsePattern($pattern);

		$modifiers = is_null($modifiers) ? ($pattern->modifiers ?: null) : $modifiers;
		$delimiter = is_null($delimiter) ? $pattern->delimiter : $delimiter;

		return new static($pattern->body, $modifiers, $delimiter);
	}


	/**
	 * Create a RegularExpressionCollection instance from an iterable Collection
	 * of patterns. This can be an array with regex strings or Pattern objects.
	 * If either the modifiers and/or delimiter are neither set on the patterns
	 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
	 * Modifiers and/or the delimiter passed as arguments will be used instead
	 * of those set on the patterns.
	 *
	 * @uses \Tea\Regex\RegularExpression::fromInstance()
	 * @uses \Tea\Regex\RegularExpression::fromString()
	 *
	 * @param  iterable              $pattern
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 *
	 * @return \Tea\Regex\RegularExpressionCollection
	 */
	public static function fromCollection($patterns, $modifiers = null, $delimiter = null)
	{
		if(!is_iterable($patterns)){
			$type = Helpers::type($patterns);
			throw new TypeError("RegularExpressionCollections can only"
				." be created from an iterable containing Tea\Regex\Contracts\Pattern objects."
				." {$type} given.");
		}

		$regexes = [];
		foreach ($patterns as $key => $pattern) {
			if($pattern instanceof Pattern)
				$regexes[] = static::fromInstance($pattern, $modifiers, $delimiter);
			elseif(Helpers::isStringable($pattern))
				$regexes[] = static::fromString($pattern, $modifiers, $delimiter);
			else
				$regexes[] = $pattern;
		}

		return new RegularExpressionCollection($regexes);
	}


}