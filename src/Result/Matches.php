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
class Matches extends MatchResult
{

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
	 *
	 * @param  mixed $default
	 * @return array
	 */
	public function groups($default = null)
	{
		$groups = array_slice($this->indexedGroups(), 1);

		if(func_num_args() === 0)
			return $groups;

		if($this->isGloabalMatch)
			return $this->replaceEmptyArray($groups, $default);
		else
			return $this->replaceEmpty($groups, $default);
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
	 * @param  mixed $default
	 * @return array
	 */
	public function named($default = null)
	{
		$groups = $this->namedGroups();

		if(func_num_args() === 0)
			return $groups;

		if($this->isGloabalMatch)
			return $this->replaceEmptyArray($groups, $default);
		else
			return $this->replaceEmpty($groups, $default);
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
	 * Convert the matches object to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->allGroups;
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
			if(!$this->offsetCaptured())
				$value = $value == "" ? Helpers::value($default) : $value;
			elseif(is_array($value))
				$value[0] = $value[0] == "" ? Helpers::value($default) : $value[0];
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
