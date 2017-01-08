<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use Tea\Regex\Flags;
use Tea\Regex\Utils\Helpers;
use Tea\Exceptions\KeyError;
use Tea\Exceptions\PropertyError;
use Tea\Regex\Exception\GroupDoesNotExist;
use Tea\Regex\Exception\InvalidGroupIndex;
use Tea\Regex\Exception\NamedGroupDoesntExist;

/**
 *
 * @todo Add support of PREG_* flags and how they affect the results' nature.
*/
class OrderedMatches extends MatchResult
{

	/**
	 * Instantiate the Matches object.
	 *
	 * @param string|array  $pattern
	 * @param string|array  $subject
	 * @param array         $matches
	 * @param bool          $hasMatch
	 * @param int           $flags
	 * @return void
	 */
	public function __construct($pattern, $subject, array $matches, $hasMatch, $flags = null)
	{
		foreach ($matches as $key => $match)
			$matches[$key] = new Matches($pattern, $subject, $match, $hasMatch, $flags, false);

		parent::__construct($pattern, $subject, $matches, $hasMatch, $flags, false);
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
		foreach ($this->allGroups as $match)
			$match->default($default);

		return $this;
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
		$groups = [];
		$nargs = func_num_args();
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $nargs ? $match->groups($default) : $match->groups();

		return $groups;
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @param  mixed $default
	 * @return array
	 */
	public function named($default = null)
	{
		$groups = [];
		$nargs = func_num_args();
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $nargs ? $match->named($default) : $match->named();

		return $groups;
	}

	/**
	 * Get all indexed groups that were captured in the match.
	 *
	 * @return array
	 */
	public function indexedGroups()
	{
		$groups = [];
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $match->indexedGroups();

		return $groups;
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	public function namedGroups()
	{
		$groups = [];
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $match->namedGroups();

		return $groups;
	}

	/**
	 * Count the number of found matches.
	 *
	 * @param  bool  $all
	 * @return int
	 */
	public function count($all = false)
	{
		if(!$all)
			return count($this->allGroups);

		$count = 0;
		foreach ($this->allGroups as $match)
			$count += $match->count();

		return $count;
	}

	/**
	 * Get an iterator for the matched groups, from 1 up to however many groups
	 * are in the pattern.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->allGroups);
	}

	/**
	 * Convert the match object to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$results = [];
		foreach ($this->allGroups as $key => $match)
			$results[$key] = $match->toArray();

		return $results;
	}

}
