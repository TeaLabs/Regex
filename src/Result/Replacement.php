<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use JsonSerializable;
use Tea\Regex\Utils\Helpers;
use Tea\Regex\Exception\InvalidReplacementKey;
use Tea\Regex\Exception\UnknownReplacementKey;
use Tea\Regex\Exception\IllegalReplacementTypeAccess;

class Replacement extends Result
{
	/**
	 * @var string|iterable
	 */
	protected $replacement;

	/**
	 * @var string|iterable
	 */
	protected $result;

	/**
	 * @var string|iterable|null
	 */
	protected $replaced;

	/**
	 * @var bool
	 */
	protected $isString;

	/**
	 * @var int
	 */
	protected $count;

	/**
	 * @var int
	 */
	protected $limit;

	/**
	 * Instantiate the Replacement object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @param string|array  $replacement
	 * @param string|array  $result
	 * @param int           $count
	 * @param int           $limit
	 *
	 * @return void
	 */
	public function __construct($pattern, $subject, $replacement, $result, $count, $limit = -1)
	{
		parent::__construct($pattern, $subject);

		$this->replacement = $replacement;
		$this->result = $result;
		$this->count = $count;
		$this->limit = $limit;
		$this->isString = !is_array($result);
	}

	/**
	 * Get one or all replacement results.
	 *
	 * @param  int|string|null $key
	 * @param  mixed $default
	 * @param  bool $orError
	 * @return mixed
	 *
	 * @uses   \Tea\Regex\Result\Replacement::has()
	 * @throws \Tea\Regex\Exception\InvalidReplacementKey
	 * @throws \Tea\Regex\Exception\UnknownReplacementKey If $orError is TRUE.
	 * @throws \Tea\Regex\Exception\IllegalReplacementTypeAccess For string replacements
	*/
	public function get($key = null, $default = null, $orError = false)
	{
		if(is_null($key))
			return $this->result;

		if($this->has($key))
			return $this->result[(string) $key];

		if($orError)
			throw UnknownReplacementKey::create($this->pattern, $this->subject, $key);

		return Helpers::value($default);
	}

	/**
	 * Determine if the given replacement item exists.
	 *
	 * @param  string  $key
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\InvalidReplacementKey
	 * @throws \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function has($key)
	{
		if($this->isString())
			throw IllegalReplacementTypeAccess::stringAsArray($this->pattern, $this->subject);

		if(!Helpers::isStringable($key))
			throw InvalidReplacementKey::create($this->pattern, $this->subject, $key);

		return array_key_exists((string) $key, $this->result);
	}

	/**
	 * Get the raw result.
	 *
	 * @return string|array
	 */
	public function result()
	{
		return $this->result;
	}

	/**
	 * Determine whether the replaced result is an array.
	 *
	 * @return bool
	 */
	public function isArray()
	{
		return !$this->isString;
	}

	/**
	 * Determine whether the replaced result is a string.
	 *
	 * @return bool
	 */
	public function isString()
	{
		return $this->isString;
	}

	/**
	 * Get the value(s) used to replace matching segments of the subject.
	 *
	 * @return string|array|\Closure
	 */
	public function replacement()
	{
		return $this->replacement;
	}

	/**
	 * Get the limit used when replacing the subject.
	 *
	 * @return int
	 */
	public function limit()
	{
		return $this->limit;
	}

	/**
	 * Get an iterator for the matched groups, from 1 up to however many groups
	 * are in the pattern.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		if($this->isString())
			throw IllegalReplacementTypeAccess::stringAsArray($this->pattern, $this->subject);

		return new ArrayIterator($this->result);
	}

	/**
	 * Count the number of found matches.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->count;
	}
	/**
	 * Determine if the given group exists.
	 *
	 * @param  string|int  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get an item's value.
	 *
	 * @param  string|int  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key, null, true);
	}

	/**
	 * Get the replaced string. If the subject was an array, the first element
	 * will be returned.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->isString() ? $this->result : Helpers::iterFirst($this->result, null, '');
	}

	/**
	 * Get the replaced array. If the subject was a string, an array with the
	 * string as the first element will be returned.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return (array) $this->result;
	}

	/**
	 * Get only the subjects that were transformed. Returns an array if the
	 * subject was an array, or a string otherwise.
	 * Returns NULL if there were no changes made on the subject(s) regardless
	 * of whether the it was a string or array.
	 *
	 * @return string|array|null
	 */
	public function replaced()
	{
		if($this->subject == $this->result)
			return null;

		if(is_array($this->result)){
			$changed = [];
			foreach ($this->result as $key => $value) {
				if($this->subject[$key] != $value)
					$changed[$key] = $value;
			}
			return $changed;
		}
		else{
			return $this->result;
		}
	}
}
