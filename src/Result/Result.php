<?php
namespace Tea\Regex\Result;

use BadMethodCallException;

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

	/**
	 * Always throws a BadMethodCallException since Match objects are immutable.
	 * Defined to meet \ArrayAccess implementation.
	 *
	 * @param  string|int  $key
	 * @param  mixed  $value
	 * @return void
	 * @throws \BadMethodCallException
	 */
	public function offsetSet($key, $value)
	{
		$type = get_class($this);
		throw new BadMethodCallException("Error setting offset '{$key}'. {$type} are immutable.");
	}

	/**
	 * Always throws a BadMethodCallException since Match objects are immutable.
	 * Defined to meet \ArrayAccess implementation.
	 *
	 * @param  string  $key
	 * @return void
	 * @throws \BadMethodCallException
	 */
	public function offsetUnset($key)
	{
		$type = get_class($this);
		throw new BadMethodCallException("Error unsetting offset '{$key}'. {$type} are immutable.");
	}

}
