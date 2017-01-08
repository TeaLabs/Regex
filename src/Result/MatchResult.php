<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use Tea\Regex\Flags;
use Tea\Regex\Utils\Helpers;
use Tea\Regex\Exception\GroupDoesNotExist;
use Tea\Regex\Exception\InvalidGroupIndex;
use Tea\Regex\Exception\NamedGroupDoesntExist;

/**
 *
 * @todo Add support of PREG_* flags and how they affect the results' nature.
*/
abstract class MatchResult extends Result
{
	/**
	 * @var bool
	 */
	protected $hasMatch;

	/**
	 * @var bool
	 */
	protected $isGloabalMatch;

	/**
	 * @var int
	 */
	protected $flags;

	/**
	 * @var array
	 */
	protected $allGroups;

	/**
	 * Instantiate the Matches object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @param array         $matches
	 * @param bool          $hasMatch
	 * @param int           $flags
	 * @param bool          $isGloabalMatch
	 * @return void
	 */
	public function __construct($pattern, $subject, array $matches, $hasMatch, $flags = 0, $isGloabalMatch = false)
	{
		parent::__construct($pattern, $subject);
		$this->allGroups = $matches;
		$this->flags = (int) $flags;
		$this->hasMatch = (bool) $hasMatch;
		$this->isGloabalMatch = (bool) $isGloabalMatch;
	}

	/**
	 * Get an array of all captured groups, both named and indexed.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->allGroups;
	}

	/**
	 * Determine if there was any match.
	 *
	 * @return bool
	 */
	public function any()
	{
		return $this->hasMatch;
	}

	/**
	 * Determine if there was any match.
	 *
	 * @return bool
	 */
	public function hasMatch()
	{
		return $this->hasMatch;
	}

	/**
	 * @return string|null
	 */
	public function result()
	{
		return $this->all();
	}

	/**
	 * Get one or more subgroups of the match or all groups if none is specified.
	 *
	 * @uses   \Tea\Regex\Result\Matches::has()
	 *
	 * @param int|string  $key
	 * @param mixed       $default
	 * @param bool        $orError
	 *
	 * @return string|array
	 *
	 * @throws \Tea\Regex\Exception\InvalidGroupIndex
	 * @throws \Tea\Regex\Exception\GroupDoesNotExist If $orError is TRUE.
	 */
	public function get($key, $default = null, $orError = false)
	{
		if($this->has($key))
			return $this->allGroups[(string) $key];

		if($orError)
			throw GroupDoesNotExist::create($this->pattern, $this->subject, $key);

		return Helpers::value($default);
	}

	/**
	 * Set the default value for match groups that came empty.
	 *
	 * @param  $default
	 *
	 * @return $this
	 */
	abstract public function default($default);

	/**
	 * Get all values that matched the captured parenthesized sub-patterns,
	 * from 1 up to however many groups are in the pattern.
	 *
	 * @param  mixed $default
	 * @return array
	 */
	abstract public function groups($default = null);

	/**
	 * Get all indexed groups that were captured in the match.
	 *
	 * @return array
	 */
	abstract public function indexedGroups();

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @param  mixed $default
	 * @return array
	 */
	abstract public function named($default = null);

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	abstract public function namedGroups();

	/**
	 * Convert the matches object to an array.
	 *
	 * @return array
	 */
	abstract public function toArray();

	/**
	 * Determine if the given group exists.
	 *
	 * @param  string  $key
	 * @return bool
	 *
	 * @throws Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function has($key)
	{
		if(!Helpers::isStringable($key))
			throw InvalidGroupIndex::create($this->pattern, $this->subject, $key);

		return array_key_exists((string) $key, $this->allGroups);
	}

	/**
	 * Determine if the match results were ordered using the PREG_SET_ORDER flag.
	 *
	 * @return boolean
	 */
	public function isSetOrder()
	{
		return Helpers::hasFlag(Flags::SET_ORDER, $this->flags);
	}

	/**
	 * Determine if the match results were ordered using the PREG_PATTERN_ORDER flag.
	 *
	 * @return boolean
	 */
	public function isPatternOrder()
	{
		return !$this->isSetOrder();
	}

	/**
	 * Determine if the match results offesets were captured by using the PREG_OFFSET_CAPTURE flag.
	 *
	 * @return boolean
	 */
	public function offsetCaptured()
	{
		return Helpers::hasFlag(Flags::OFFSET_CAPTURE, $this->flags);
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
}
