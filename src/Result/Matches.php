<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use Tea\Regex\Helpers;
use BadMethodCallException;
use Tea\Regex\Exception\GroupDoesNotExist;
use Tea\Regex\Exception\InvalidGroupIndex;
use Tea\Regex\Exception\NamedGroupDoesntExist;

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
	 * Instantiate the Matches object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @param bool          $hasMatch
	 * @param array         $matches
	 * @return void
	 */
	public function __construct($pattern, $subject, array $matches, $hasMatch, $isGloabalMatch = false)
	{
		parent::__construct($pattern, $subject);
		$this->allGroups = $matches;
		$this->hasMatch = (bool) $hasMatch;
		$this->isGloabalMatch = (bool) $isGloabalMatch;
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
	 * @return string|null
	 */
	public function result()
	{
		return $this->all();
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
	 * Get one or more subgroups of the match or all groups if none is specified.
	 *
	 * @param int|string|null $key
	 * @param mixed $default
	 * @param bool $orError
	 *
	 * @return string|array
	 *
	 * @throws Tea\Regex\Exception\GroupDoesNotExist If $orError is TRUE.
	 * @throws Tea\Regex\Exception\InvalidGroupIndex If $orError is TRUE.
	 */
	public function get($key = null, $default = null, $orError = false)
	{
		if(is_null($key))
			return $this->indexedGroups();

		if(Helpers::isStringable($key) && $this->has($key))
			return $this->allGroups[(string) $key];

		if($orError){
			if(!Helpers::isStringable($key))
				throw InvalidGroupIndex::create($this->pattern, $this->subject, $key);
			else
				throw GroupDoesNotExist::create($this->pattern, $this->subject, $key);
		}

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
	public function groups($namedGroups = false)
	{
		return $namedGroups ? $this->namedGroups() : array_slice($this->indexedGroups(), 1);
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
	 * Get all indexed groups that were captured in the match.
	 *
	 * @return array
	 */
	public function indexedGroups()
	{
		if(is_null($this->indexedGroups)){
			$this->indexedGroups = [];
			foreach ($this->allGroups as $key => $value)
				if(is_int($key))
					$this->indexedGroups[$key] = $value;
		}

		return $this->indexedGroups;
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	public function namedGroups()
	{
		if(is_null($this->namedGroups)){
			$this->namedGroups = [];
			foreach ($this->allGroups as $key => $value)
				if(!is_int($key))
					$this->namedGroups[$key] = $value;
		}

		return $this->namedGroups;
	}

	/**
	 * Count the number of found matches.
	 *
	 * @return int
	 */
	public function count()
	{
		$groups = $this->groups();
		return $this->isGloabalMatch
				? count($groups, COUNT_RECURSIVE) - count($groups)
				: count($groups);
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
		if(!is_numeric($key) && is_string($key) && array_key_exists($key, $this->namedGroups()))
			return $this->namedGroups[$key];

		if(is_numeric($key) || !is_string($key))
			throw InvalidGroupIndex::create($this->pattern, $this->subject, $key);
		else
			throw NamedGroupDoesntExist::create($this->pattern, $this->subject, $key);
	}

}
