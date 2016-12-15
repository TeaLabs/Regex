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
	protected $pattern;

	/**
	 * @var string
	 */
	protected $modifiers;

	/**
	 * @var string
	 */
	protected $dilimiter;

	/**
	 * Instantiate the Regular Expression instance.
	 *
	 * @param  string   $pattern
	 * @param  string|null   $modifiers
	 * @return void
	 */
	public function __construct($pattern, $modifiers = null)
	{
		$this->pattern = $pattern;
		$this->modifiers = is_null($modifiers) ? Config::modifiers() : $modifiers;
		$this->dilimiter = Config::delimiter();
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
	public function is($pattern, $subject, $flags =0, $offset = 0)
	{
		$matches = null;
		return (bool) preg_match(static::addModifiers($pattern), $subject, $matches, $flags, $offset);
	}


	/**
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\MatchResult
	 */
	public function match($subject, $flags = 0, $offset = 0)
	{
		return MatchResult::for($this, $subject, $flags, $offset);
	}

	/**
	 * @param string $subject
	 * @param int $flags
	 * @param int $offset
	 *
	 * @return \Tea\Regex\MatchAllResult
	 */
	public function matchAll($subject, $flags = 0, $offset = 0)
	{
		return MatchAllResult::for($this, $subject, $flags, $offset);
	}

	/**
	 * @param string|callable $replacement
	 * @param string          $subject
	 * @param int             $limit
	 *
	 * @return \Tea\Regex\ReplaceResult
	 */
	public function replace($replacement, $subject, $limit = -1)
	{
		try {
			list($result, $count) = is_callable($replacement) ?
				static::doReplacementWithCallable($pattern, $replacement, $subject, $limit) :
				static::doReplacement($pattern, $replacement, $subject, $limit);
		} catch (Exception $exception) {
			throw RegexFailed::replace($pattern, $subject, $exception->getMessage());
		}

		if ($result === null) {
			throw RegexFailed::replace($pattern, $subject, static::lastPregError());
		}

		return ReplaceResult::for($this, $replacement, $subject, $limit);
	}

	/**
	 * Get the regex pattern string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->dilimiter.$this->pattern.$this->dilimiter.$this->modifiers;
	}
}