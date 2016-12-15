<?php
namespace Tea\Regex\Result;

abstract class Result implements ResultInterface
{

	/**
	 * @var string|array
	 */
	protected $pattern;

	/**
	 * @var string|array
	 */
	protected $subject;

	/**
	 * Instantiate the Result object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @return void
	 */
	public function __construct($pattern, $subject)
	{
		$this->pattern = $pattern;
		$this->subject = $subject;
	}

	/**
	 * Determine if there is any result.
	 *
	 * @return bool
	 */
	public function any()
	{
		return $this->count() > 0;
	}

	/**
	 * Get the regex pattern(s) that produced this result.
	 *
	 * @return string|array
	 */
	public function pattern()
	{
		return $this->pattern;
	}

	/**
	 * Get the subject(s) string(s) that produced this result.
	 *
	 * @return string|array
	 */
	public function subject()
	{
		return $this->subject;
	}


	protected static function lastPregError()
	{
		return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
	}

}
