<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
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
class OrderedMatches extends Matches
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
		$flags = Helpers::removeFlag(PREG_SET_ORDER, $flags);

		foreach ($matches as $key => $match)
			$matches[$key] = new Matches($pattern, $subject, $match, true, false, $flags);

		parent::__construct($pattern, $subject, $matches, $hasMatch, false, $flags);
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
			$results[ (string) $group] = $this->get($group, null, true);

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
		$groups = [];
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $match->groups();

		return $groups;
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
		if(!Helpers::isStringable($key) || ((string)(int)(string) $key) != ((string) $key))
			throw InvalidGroupIndex::create($this->pattern, $this->subject, $key, "Expected an Integer.");

		return array_key_exists((string) $key, $this->allGroups);
	}

	/**
	 * Get all named groups that were captured in the match.
	 *
	 * @return array
	 */
	public function named()
	{
		$groups = [];
		foreach ($this->allGroups as $key => $match)
			$groups[$key] = $match->named();

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
	 * @return int
	 */
	public function count()
	{
		$count = 0;
		foreach ($this->allGroups as $match)
			$count += $match->count();

		return $count;
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
		throw PropertyError::accessNotAllowed($key, $this);
	}

	/**
	 * Determine if the match results were ordered using the PREG_SET_ORDER flag.
	 *
	 * @return boolean
	 */
	public function isSetOrder()
	{
		return true;
	}
}
