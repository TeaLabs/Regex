<?php
namespace Tea\Regex\Result;

use Countable;
use ArrayAccess;
use IteratorAggregate;

interface ResultInterface extends Countable, ArrayAccess, IteratorAggregate
{
	/**
	 * Get one or all results.
	 *
	 * @param  int|string|null $key
	 * @param  mixed $default
	 * @return mixed
	*/
	public function get($key = null, $default = null);

	/**
	 * Determine if there was any result
	 *
	 * @return bool
	 */
	public function any();

	/**
	 * Get the regex pattern(s) that produced this result.
	 *
	 * @return string|array
	 */
	public function pattern();

	/**
	 * Get the subject(s) string(s) that produced this result.
	 *
	 * @return string|array
	 */
	public function subject();

	/**
	 * Get the raw result.
	 *
	 * @return mixed
	 */
	public function result();
}
