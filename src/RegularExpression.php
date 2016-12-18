<?php
namespace Tea\Regex;

/**
*
*/
class RegularExpression
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
	 *
	 * @param  string              $body
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 * @return void
	 */
	public function __construct($body, $modifiers = null, $delimiter = null)
	{
		$this->body = $body;
		$this->modifiers = $modifiers === false ? null : ($modifiers ?: Config::modifiers());
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
	 * @param  bool    $invert
	 *
	 * @return array
	 *
	 * @throws \Tea\Regex\Exception\FilterError
	*/
	public function filter(array $input, $invert = false)
	{
		return Adapter::filter($this, $input, $invert);
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @see  \Tea\Regex\Adapter::is()
	 *
	 * @param string $subject
	 * @param int $offset
	 * @param int $flags
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function is($subject, $offset = 0, $flags =0)
	{
		return Adapter::is($this, $subject, $offset, $flags);
	}

	/**
	 * Perform a regular expression match on given subject.
	 *
	 * @see  \Tea\Regex\Adapter::match()
	 *
	 * @param string $subject
	 * @param int $offset
	 * @param int $flags
	 *
	 * @return \Tea\Regex\Result\Matches
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function match($subject, $offset = 0, $flags = 0)
	{
		return Adapter::match($this, $subject, $offset, $flags);
	}

	/**
	 * Perform a global regular expression match on given subject.
	 *
	 * @see  \Tea\Regex\Adapter::matchAll()
	 *
	 * @param string $subject
	 * @param int $offset
	 * @param int $flags
	 *
	 * @return \Tea\Regex\Result\Matches
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function matchAll($subject, $offset = 0, $flags = 0)
	{
		return Adapter::matchAll($this, $subject, $offset, $flags);
	}

	/**
	 * Determine if the given string matches the given regex pattern.
	 *
	 * @see  \Tea\Regex\Adapter::matches()
	 *
	 * @param string $subject
	 * @param int $offset
	 * @param int $flags
	 *
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\MatchError
	 */
	public function matches($subject, $offset = 0, $flags =0)
	{
		return Adapter::matches($this, $subject, $offset, $flags);
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
		return Adapter::replace($this, $replacement, $subject, $limit);
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
		return Adapter::replaceCallback($this, $callback, $subject, $limit);
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
		return Adapter::replaced($this, $replacement, $subject, $limit);
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
		return Adapter::split($this, $subject, $limit, $flags);
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
	 * Cast the Regular Expression object to a string.
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
	 * or another RegularExpression instance.
	 * If the given pattern is string it will be parsed to extract the delimiter
	 * and modifiers if any, and the regex body. If either the delimiter or
	 * modifiers are missing, the defaults (as set in \Tea\Regex\Config) will
	 * be used.
	 *
	 * @param  string|\Tea\Regex\RegularExpression  $pattern
	 * @param  string|null|false                    $modifiers
	 * @param  string|null                          $delimiter
	 * @return static
	 */
	public static function from($pattern, $modifiers = null, $delimiter = null)
	{
		if($pattern instanceof self)
			return static::fromInstance($pattern, $modifiers, $delimiter);
		else
			return static::fromString($pattern, $modifiers, $delimiter);
	}

	/**
	 * Create a RegularExpression instance from another RegularExpression instance.
	 *
	 * @param  \Tea\Regex\RegularExpression  $pattern
	 * @param  string|null|false             $modifiers
	 * @param  string|null                   $delimiter
	 * @return static
	 */
	public static function fromInstance(RegularExpression $pattern, $modifiers = null, $delimiter = null)
	{
		$modifiers = is_null($modifiers)
			? ($pattern->getModifiers() === null ? false : $pattern->getModifiers()) : $modifiers;
		$delimiter = is_null($delimiter) ? $pattern->getDelimiter() : $delimiter;

		return new static($pattern->getBody(), $modifiers, $delimiter);
	}

	/**
	 * Create a RegularExpression instance from a possibly complete regex string.
	 * The given regex string will be parsed to extract the delimiter and modifiers
	 * if any, and the regex body. If either the delimiter or modifiers are missing,
	 * the defaults (as set in \Tea\Regex\Config) will be used.
	 *
	 * @param  string  $pattern
	 * @return static
	 */
	public static function fromString($pattern, $modifiers = null, $delimiter = null)
	{
		$pattern = Adapter::parsePattern($pattern);

		$modifiers = is_null($modifiers) ? $pattern->modifiers : $modifiers;
		$delimiter = is_null($delimiter) ? $pattern->delimiter : $delimiter;

		return new static($pattern->body, $modifiers, $delimiter);
	}


}