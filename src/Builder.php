<?php
namespace Tea\Regex;

use BadMethodCallException;
use Tea\Regex\Contracts\Pattern;
use Gherkins\RegExpBuilderPHP\RegExpBuilder;
use Tea\Regex\Exception\InvalidRegexPatternException;

/**
*
*/
class Builder extends RegExpBuilder implements Pattern
{
	/**
	 * @var string
	 */
	protected $modifiers;

	/**
	 * @var string
	 */
	protected $delimiter;

	/**
	 * Instantiate the Builder instance. If either the delimiter and/or the
	 * modifiers are not provided, the defaults {@see \Tea\Regex\Config} will
	 * be used.
	 *
	 * @param  string|null  $delimiter
	 * @param  string|null  $modifiers
	 */
	public function __construct($delimiter = null, $modifiers = null)
	{
		parent::__construct();

		$this->delimiter = $delimiter && Config::validateDelimiter($delimiter)
				? $delimiter : Config::delimiter();

		$modifiers = is_null($modifiers) ? Config::modifiers() : $modifiers;
		$this->modifier($modifiers);
	}

	/**
	 * Add the given modifiers to the regex.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param string|iterable $modifiers
	 * @return $this
	 */
	public function modifiers($modifiers)
	{
		$modifiers = Helpers::isNoneStringIterable($modifiers)
				? $modifiers : str_split((string) $modifiers);
		$new  = '';
		foreach ($modifiers as $modifier) {
			$modifier = trim((string) $modifier);
			if($modifier && !$this->hasModifier($modifier))
				$new .= $modifier;
		}

		if($new && Modifiers::validate($new))
			$this->modifiers .= $new;


		return $this;
	}

	/**
	 * Add the given modifier to the regex.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string  $modifier
	 * @return $this
	 */
	public function modifier($modifier)
	{
		$modifier = trim((string) $modifier);

		if(strlen($modifier) > 1)
			return $this->modifiers($modifier);

		if($modifier && Modifiers::validate($modifier) && !$this->hasModifier($modifier))
			$this->modifiers .= $modifier;

		return $this;
	}

	/**
	 * Remove the given modifier.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string   $modifier
	 * @return $this
	 */
	public function removeModifier($modifier)
	{
		$modifier = trim((string) $modifier);

		if(strlen($modifier) > 1)
			return $this->removeModifiers($modifier);

		if($modifier)
			$this->modifiers = str_replace($modifier, '', $this->modifiers);

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
		$modifiers = Helpers::isNoneStringIterable($modifiers)
						? $modifiers : str_split((string) $modifiers);

		$this->modifiers = str_replace($modifiers, '', $this->modifiers);

		return $this;
	}

	/**
	 * Determine whether the regex has any of the given modifier.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string   $modifier
	 * @return bool
	 */
	public function hasModifier($modifier)
	{
		if(strlen($modifier) > 1)
			return $this->hasModifiers($modifier);

		return strpos($this->modifiers, $modifier) !== false;
	}

	/**
	 * Determine whether the regex has all or any of the given modifiers.
	 * By default, this method checks if the regex has all the given modifiers.
	 * But accepts an optional $any which if set to TRUE, will return TRUE if
	 * at least one of the given modifiers is set.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string|iterable   $modifiers
	 * @param  bool              $any
	 * @return bool
	 */
	public function hasModifiers($modifiers, $any = false)
	{
		$modifiers = Helpers::isNoneStringIterable($modifiers)
						? $modifiers : str_split((string) $modifiers);

		foreach ($modifiers as $modifier) {
			$has = strpos($this->modifiers, $modifier) !== false;
			if(($any && $has) || (!$any && !$has))
				return $has;
		}
		return $any ? false : true;
	}

	/**
	 * Add or Remove the Modifiers::CASELESS modifier. If $enable is passed as
	 * FALSE, this modifier will be removed. Otherwise if TRUE or not specified,
	 * it will be added.
	 *
	 * @see \Tea\Regex\Modifiers::CASELESS
	 *
	 * @param  bool   $enable
	 * @return $this
	 */
	public function ignoreCase($enable = true)
	{
		if($enable)
			return $this->modifier(Modifiers::CASELESS);
		else
			return $this->removeModifier(Modifiers::CASELESS);
	}

	/**
	 * Add or Remove the Modifiers::MULTILINE modifier. If $enable is passed as
	 * FALSE, this modifier will be removed. Otherwise if TRUE or not specified,
	 * it will be added.
	 *
	 * @see \Tea\Regex\Modifiers::MULTILINE
	 *
	 * @param  bool   $enable
	 * @return $this
	 */
	public function multiLine($enable = true)
	{
		if($enable)
			return $this->modifier(Modifiers::MULTILINE);
		else
			return $this->removeModifier(Modifiers::MULTILINE);
	}


	public function of($s)
	{
		$this->_of = $this->sanitize($s);

		return $this;
	}


	public function from($s)
	{
		$this->_from = $this->sanitize(join("", $s));

		return $this;
	}

	public function notFrom($s)
	{
		$this->_notFrom = $this->sanitize(join("", $s));

		return $this;
	}

	/**
	 * Quote/escape regular expression characters in given value.
	 *
	 * @param  string   $value
	 * @return string
	 */
	protected function sanitize($value)
	{
		return Adapter::quote($value, $this->delimiter);
	}

	/**
	 * Create a new builder instance. Unless a value of FALSE is provided, the
	 * delimiter of the current instance will be used on the new instance.
	 *
	 * @param  string|null|false  $delimiters
	 * @param  string|null        $modifiers
	 *
	 * @return \Tea\Regex\Builder
	 */
	public function getNew($delimiter = null, $modifiers = null)
	{
		$delimiter = $delimiter === false ? null : ($delimiter ?: $this->getDelimiter());
		return new static($delimiter, $modifiers);
	}


	/**
	 * Get the regex pattern body.
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->getLiteral();
	}

	/**
	 * Get the regex pattern delimiter.
	 *
	 * @return string
	 */
	public function getDelimiter()
	{
		return $this->delimiter;
	}

	/**
	 * Get a string of all the set modifiers.
	 *
	 * @return string
	 */
	public function getModifiers()
	{
		return $this->modifiers;
	}

	/**
	 * Compile the buildup into a RegularExpression.
	 *
	 * @see \Tea\Regex\RegularExpression
	 *
	 * @return \Tea\Regex\RegularExpression
	 */
	public function compile()
	{
		return new RegularExpression($this->getBody(), $this->getModifiers(), $this->getDelimiter());
	}

	/**
	 * Get the Compiled string version the current pattern.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->compile();
	}

/*** Overrides for incompatible methods ***/

	/**
	 * Unlike {@see \Gherkins\RegExpBuilderPHP\RegExpBuilder::globalMatch()}, this
	 * method throws a \BadMethodCallException exception.
	 *
	 * @see \Tea\Regex\RegularExpression::matchAll() for performing global matches.
	 * @see \Tea\Regex\Builder::compile() for converting to \Tea\Regex\RegularExpression
	 *
	 * @return void
	 * @throws \BadMethodCallException
	 */
	public function globalMatch()
	{
		throw new BadMethodCallException("Method 'globalMatch' is not supported. "
			."To perform global matches, use \Tea\Regex\RegularExpression::matchAll(). "
			."You can compile the builder to a RegularExpression by calling the 'compile()' method.");
	}


	/**
	 * Unlike {@see \Gherkins\RegExpBuilderPHP\RegExpBuilder::pregMatchFlags()},
	 * this method throws a \BadMethodCallException exception.
	 *
	 * @see \Tea\Regex\RegularExpression::match() for how to set the flags
	 * @see \Tea\Regex\RegularExpression::matchAll() for how to set the flags
	 * @see \Tea\Regex\Builder::compile() for converting to \Tea\Regex\RegularExpression
	 *
	 * @return void
	 * @throws \BadMethodCallException
	 */
	public function pregMatchFlags($flags = null)
	{
		throw new BadMethodCallException("Method 'pregMatchFlags' is not supported. "
			."The flags should be passed as arguments to the specific 'match' methods on \Tea\Regex\RegularExpression.");
	}

	/**
	 * Unlike {@see \Gherkins\RegExpBuilderPHP\RegExpBuilder::getRegExp()}, this
	 * method throws a \BadMethodCallException exception. This is to prevent the
	 * use of {@see \Gherkins\RegExpBuilderPHP\RegExp} which is incompatible with
	 * this library.
	 *
	 * Use {@see \Tea\Regex\Builder::compile()} instead.
	 *
	 * @return void
	 * @throws \BadMethodCallException
	 */
	public function getRegExp()
	{
		throw new BadMethodCallException("Method 'getRegExp' is not supported. "
			."Use \Tea\Regex\Builder::compile() instead.");
	}

/*** End: Overrides for incompatible methods ***/

}