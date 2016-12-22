<?php
namespace Tea\Regex;

use TypeError;
use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Tea\Regex\Utils\Arr;
use Tea\Regex\Utils\Helpers;
use OutOfBoundsException;
use Tea\Contracts\Regex\Pattern;

/**
*
*/
class RegularExpressionCollection extends RegularExpression implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * @var array
	 */
	protected $patterns = [];

	/**
	 * Instantiate the RegularExpressionCollection instance.
	 *
	 * @param  array  $expressions
	 * @return void
	 */
	public function __construct(array $patterns = [], $modifiers = null, $delimiter = null)
	{
		parent::__construct(null, $modifiers, $delimiter);

		// if(!is_iterable($patterns)){
		// 	$type = Helpers::type($patterns);
		// 	throw new TypeError("RegularExpressionCollections can only"
		// 		." be created from an iterable containing Tea\Regex\Contracts\Pattern objects."
		// 		." {$type} given.");
		// }

		foreach ($patterns as $pattern)
			$this[] = $pattern;
	}

	/**
	 * Return the first pattern in the collection.
	 *
	 * @return Tea\Regex\Contracts\Pattern
	 */
	public function getPattern()
	{
		if(empty($this->patterns))
			throw new OutOfBoundsException("Error getting pattern. RegularExpressionCollection is empty.");

		return $this->patterns[0];
	}

	/**
	 * Return an array of all patterns.
	 *
	 * @return array
	 */
	public function getPatterns()
	{
		return $this->patterns;
	}

	/**
	 * Returns the body of the first pattern.
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->getPattern()->getBody();
	}

	/**
	 * Returns the modifiers of the first pattern.
	 *
	 * @return string
	 */
	public function getModifiers()
	{
		return $this->getPattern()->getModifiers();
	}

	/**
	 * Returns the delimiter of the first pattern.
	 *
	 * @return string
	 */
	public function getDelimiter()
	{
		return  $this->getPattern()->getDelimiter();
	}


	/**
	 * Add the given modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @return void
	 */
	public function addModifiers($modifiers)
	{
		parent::addModifiers($modifiers);

		foreach ($this->patterns as $pattern)
			$pattern->addModifiers($modifiers);

		return $this;
	}

	/**
	 * Remove the given modifiers.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @return $this
	 */
	public function removeModifiers($modifiers)
	{
		parent::removeModifiers($modifiers);

		foreach ($this->patterns as $pattern)
			$pattern->removeModifiers($modifiers);

		return $this;
	}


	/**
	 * Get the complied value of the RegularExpression. This method returns
	 * $this since it already implements __toString() making it compatible.
	 *
	 * @return array
	 */
	public function compile()
	{
		return $this->count() > 0 ? $this->toArray() : $this->getPattern();
	}

	/**
	 * Return an array of all patterns.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->patterns;
	}

	/**
	 * Get the number of patterns in the collection.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->patterns);
	}

	/**
	 * Get an iterator for the matched groups, from 1 up to however many groups
	 * are in the pattern.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}

	/**
	 * Determine if a pattern exists at an offset.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->patterns);
	}

	/**
	 * Get a pattern at a given offset.
	 *
	 * @param  mixed  $key
	 * @return Tea\Regex\Contracts\Pattern
	 */
	public function offsetGet($key)
	{
		return $this->patterns[$key];
	}

	/**
	 * Set the pattern at a given offset.
	 *
	 * @param  mixed  $key
	 * @param  mixed $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if($value instanceof Pattern)
			$this->patterns[] = $value;
		elseif(Helpers::isStringable($value))
			$this->patterns[] = $this->createPattern($value);
		elseif (Arr::accessible($value)) {
			$this->patterns[] = $this->createPattern(
				$value[0],
				(Arr::exists($value, 1) ? $value[1] : null),
				(Arr::exists($value, 2) ? $value[2] : null)
			);
		}
		else{
			$type = Helpers::type($value);
			throw new TypeError("Error adding RegularExpressionCollection pattern."
				." Pattern should be a Tea\Regex\Contracts\Pattern, ArrayAccess, String or Array."
				." {$type} given.");
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->patterns[$key]);
	}

	protected function createPattern($body, $modifiers = null, $delimiter = null)
	{
		if(is_null($modifiers)) $modifiers = $this->modifiers;
		if(is_null($delimiter)) $delimiter = $this->delimiter;
		return new RegularExpression($body, $modifiers, $delimiter);
	}

}