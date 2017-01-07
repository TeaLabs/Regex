<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use Tea\Regex\Utils\Helpers;
use Tea\Regex\Exception\GroupDoesNotExist;
use Tea\Regex\Exception\InvalidGroupIndex;
use Tea\Regex\Exception\NamedGroupDoesntExist;

/**
 *
 * @todo Add support of PREG_* flags and how they affect the results' nature.
*/
class Matches extends Result
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
	 * @var array
	 */
	protected $indexedGroups;

	/**
	 * @var array
	 */
	protected $namedGroups;

	/**
	 * @var int
	 */
	protected $count;

	/**
	 * Instantiate the Matches object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @param array         $matches
	 * @param bool          $hasMatch
	 * @param bool          $isGloabalMatch
	 * @param int           $flags
	 * @return void
	 */
	public function __construct($pattern, $subject, array $matches, $hasMatch, $isGloabalMatch = false, $flags = null)
	{
		parent::__construct($pattern, $subject);
		$this->allGroups = $matches;
		$this->hasMatch = (bool) $hasMatch;
		$this->isGloabalMatch = (bool) $isGloabalMatch;
		$this->flags = (int) $flags;
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
	 * Set the default value for match groups that came empty.
	 *
	 * @param  $default
	 *
	 * @return $this
	 */
	public function default($default)
	{
		if($this->isGloabalMatch)
			$this->allGroups = $this->replaceEmptyArray($this->allGroups, $default);
		else
			$this->allGroups = $this->replaceEmpty($this->allGroups, $default);

		$this->parseGroups();

		return $this;
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
	 * Get one or more groups of the match.
	 *
	 * @param int|string ...$groups
	 *
	 * @return string|array
	 *
	 * @throws Tea\Regex\Exception\GroupDoesNotExist
	 * @throws Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function group($groups)
	{
		if(func_num_args() === 1)
			return $this->get($groups, null, true);

		$results = [];
		foreach (func_get_args() as $group)
			$results[$group] = $this->get($group, null, true);

		return $results;
	}

	/**
	 * Get all values that matched the captured parenthesized sub-patterns,
	 * from 1 up to however many groups are in the pattern.
	 * If namedGroups is passed as TRUE, named groups are returned instead.
	 *
	 * @param bool $namedGroups
	 * @return array
	 */
	public function groups()
	{
		return array_slice($this->indexedGroups(), 1);
	}

	/**
	 * Get all indexed groups that were captured in the match.
	 *
	 * @return array
	 */
	public function indexedGroups()
	{
		if(is_null($this->indexedGroups))
			$this->parseGroups();

		return $this->indexedGroups;
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	public function named()
	{
		return $this->namedGroups();
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	public function namedGroups()
	{
		if(is_null($this->namedGroups))
			$this->parseGroups();

		return $this->namedGroups;
	}

	/**
	 * Count the number of found matches.
	 *
	 * @return int
	 */
	public function count()
	{
		if(!is_null($this->count))
			return $this->count;

		$groups = $this->groups();

		if(!$this->isGloabalMatch)
			return $this->count = count($groups);

		if(!$this->offsetCaptured())
			return $this->count = count($groups, COUNT_RECURSIVE) - count($groups);

		$count = 0;
		foreach ($groups as $group)
			$count += count($group);

		return $this->count = $count;
	}

	/**
	 * Get an iterator for the matched groups, from 1 up to however many groups
	 * are in the pattern.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->groups());
	}

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
	 * Magic method for getting named groups as class properties.
	 *
	 * @param  string  $key
	 * @return string|array
	 *
	 * @throws Tea\Regex\Exception\NamedGroupDoesntExist
	 */
	public function __get($key)
	{
		if(array_key_exists($key, $this->namedGroups()))
			return $this->namedGroups[$key];

		if(is_numeric($key) || !Helpers::isStringable($key))
			throw InvalidGroupIndex::create($this->pattern, $this->subject, $key);
		else
			throw NamedGroupDoesntExist::create($this->pattern, $this->subject, $key);
	}

	/**
	 * Determine if the match results offesets were captured by using the PREG_OFFSET_CAPTURE flag.
	 *
	 * @return boolean
	 */
	public function offsetCaptured()
	{
		return Helpers::hasFlag(PREG_OFFSET_CAPTURE, $this->flags);
	}

	/**
	 * Determine if the match results were ordered using the PREG_SET_ORDER flag.
	 *
	 * @return boolean
	 */
	public function isSetOrder()
	{
		return Helpers::hasFlag(PREG_SET_ORDER, $this->flags);
	}

	/**
	 * Determine if the match results were ordered using the PREG_PATTERN_ORDER flag.
	 *
	 * @return boolean
	 */
	public function isPatternOrder()
	{
		return ! $this->isSetOrder();
	}

	/**
	 * Extract the indexed and named groups from the $this->allGroups array
	 *
	 * @return void
	 */
	protected function parseGroups()
	{
		$this->indexedGroups = [];
		$this->namedGroups = [];

		foreach ($this->allGroups as $key => $value) {
			if(is_int($key))
				$this->indexedGroups[$key] = $value;
			else
				$this->namedGroups[$key] = $value;
		}
	}

	/**
	 * Replace empty match groups with a default value.
	 *
	 * @param  array  $groups
	 * @param  mixed $default
	 * @return array
	 */
	protected function replaceEmpty(array $groups, $default)
	{
		foreach ($groups as &$value) {
			if($this->offsetCaptured() && is_array($value))
				if($value[0] == "")
					$value[0] = $default;
			elseif($value == "")
				$value = $default;
		}

		return $groups;
	}

	/**
	 * Replace empty matches in the group arrays with a default value.
	 *
	 * @param  array  $groups
	 * @param  mixed  $default
	 * @return array
	 */
	protected function replaceEmptyArray(array $groups, $default)
	{
		foreach ($groups as &$value)
			$value = $this->replaceEmpty($value, $default);

		return $groups;
	}

}
