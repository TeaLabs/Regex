<?php
namespace Tea\Regex;

use Tea\Regex\Exception\InvalidRegexPatternException;

/**
*
*/
class Builder
{

	// protected $modifiers = [
	// 	'u' => null,
	// 	'i' => null,
	// 	'm' => null,
	// 	's' => null,
	// 	'x' => null,
	// 	'A' => null,
	// 	'D' => null,
	// 	'S' => null,
	// 	'U' => null,
	// 	'X' => null,
	// 	'J' => null
	// ];

	/**
	 * @var array
	 */
	protected $modifiers = '';

	/**
	 * @var string
	 */
	protected $delimiter;

	/**
	 * @var string
	 */
	protected $_pregMatchFlags = "";

	/**
	 * @var array
	 */
	protected $_literal = array();

	/**
	 * @var int
	 */
	protected $_groupsUsed = 0;

	/**
	 * @var int
	 */
	protected $_min;

	/**
	 * @var int
	 */
	protected $_max;

	/**
	 * @var string
	 */
	protected $_of;

	/**
	 * @var string
	 */
	protected $_ofAny;

	/**
	 * @var string
	 */
	protected $_ofGroup;

	/**
	 * @var string
	 */
	protected $_from;

	/**
	 * @var string
	 */
	protected $_notFrom;

	/**
	 * @var string
	 */
	protected $_like;

	/**
	 * @var string
	 */
	protected $_either;

	/**
	 * @var bool
	 */
	protected $_reluctant;

	/**
	 * @var bool
	 */
	protected $_capture;

	/**
	 * @var string
	 */
	protected $_captureName;

	/**
	 * Instantiate the Builder instance. Accepts an optional pattern from which
	 * the builder can be created. The pattern can be another Builder instance
	 * or a raw regex string.
	 *
	 * Throws a InvalidRegexPatternException if the given pattern is not a Builder
	 * instance and can't be converted to string.
	 *
	 * @param  string|\Tea\Regex\Builder|null   $pattern
	 * @return void
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public function __construct($pattern = null)
	{
		if($pattern instanceof self){
			$this->modifiers($pattern->getModifiers());
			$this->_literal[] = $this->combineGroupNumberingAndGetLiteral($pattern);
		}
		elseif($pattern){
			if(!is_stringable($pattern)){
				throw new InvalidRegexPatternException($pattern);
			}

			$components = static::parsePattern($pattern);
			if($components['modifiers'])
				$this->modifiers($components['modifiers']);

			if($components['literal'])
				$this->_literal[] = $this->combineGroupNumberingAndGetLiteral($components['literal']);
		}

		$this->clear();
	}

	public function __toString()
	{
		return $this->compile();
	}


	public function compile($modifiers = null)
	{
		if($modifiers) $this->modifiers($modifiers);
		return $this->_delimiter . $this->getLiteral() . $this->_delimiter . $this->getModifiers();
	}


	/**
	 * reset values
	 */
	protected function clear()
	{
		$this->_min       = -1;
		$this->_max       = -1;
		$this->_of        = "";
		$this->_ofAny     = false;
		$this->_ofGroup   = -1;
		$this->_from      = "";
		$this->_notFrom   = "";
		$this->_like      = "";
		$this->_either    = "";
		$this->_reluctant = false;
		$this->_capture   = false;
	}

	protected function flushState()
	{
		if ($this->_of != "" || $this->_ofAny || $this->_ofGroup > 0 || $this->_from != ""
			|| $this->_notFrom != "" || $this->_like != "")
		{
			$captureLiteral   = $this->_capture
					? ($this->_captureName ? "?P<".$this->_captureName.">" : "")
					: "?:";
			$quantityLiteral  = $this->getQuantityLiteral();
			$characterLiteral = $this->getCharacterLiteral();
			$reluctantLiteral = $this->_reluctant ? "?" : "";
			$this->_literal[] = ("(" . $captureLiteral . "(?:" . $characterLiteral . ")" . $quantityLiteral . $reluctantLiteral . ")");
			$this->clear();
		}
	}

	protected function getQuantityLiteral()
	{
		if ($this->_min != -1) {
			if ($this->_max != -1) {
				return "{" . $this->_min . "," . $this->_max . "}";
			}

			return "{" . $this->_min . ",}";
		}

		return "{0," . $this->_max . "}";
	}

	protected function getCharacterLiteral()
	{
		if ($this->_of != "") {
			return $this->_of;
		}
		if ($this->_ofAny) {
			return ".";
		}
		if ($this->_ofGroup > 0) {
			return "\\" . $this->_ofGroup;
		}
		if ($this->_from != "") {
			return "[" . $this->_from . "]";
		}
		if ($this->_notFrom != "") {
			return "[^" . $this->_notFrom . "]";
		}
		if ($this->_like != "") {
			return $this->_like;
		}

	    // @codeCoverageIgnoreStart
		return null;
	    // @codeCoverageIgnoreEnd
	}

	public function getLiteral()
	{
		$this->flushState();

		return join("", $this->_literal);
	}

	/**
	 * Update group numbers in the given regex as well as this instance's group
	 * count and return the new literal.
	 *
	 * @internal
	 *
	 * @param  static|string $regex
	 * @return string
	 */
	protected function combineGroupNumberingAndGetLiteral($regex)
	{
		$literal = $this->incrementGroupNumbering(
								$this->getLiteralFromRegex($regex),
								$this->_groupsUsed
					);
		$this->_groupsUsed += $this->getGroupsUsedFromRegex($regex);

		return $literal;
	}

	/**
	 * Increment capture group back-references in pattern by the given value.
	 *
	 * @internal
	 *
	 * @param  string $pattern
	 * @param  int    $increment
	 * @return string  The updated pattern
	 */
	protected function incrementGroupNumbering($pattern, $increment)
	{

		if ($increment > 0) {
			$pattern = preg_replace_callback(
				'/\\\(\d+)/'.Modifiers::UTF8,
				function ($groupReference) use ($increment) {
					$groupNumber = (int)substr($groupReference[0], 1) + $increment;

					return sprintf("\\%s", $groupNumber);
				}, $pattern);
		}

		return $pattern;
	}

	/**
	 * Get the number of capture groups in regex object or pattern.
	 *
	 * @todo Add a parser for matching the number of groups if regex is string.
	 * @internal
	 *
	 * @param  \Tea\Regex\Builder|string $regex
	 * @return int
	 */
	protected function getGroupsUsedFromRegex($regex)
	{
		if($regex instanceof self)
			return $regex->_groupsUsed;

		return 0;

		// $pattern = '/(\((?=[^\?\\\d])[^()]*(?:\((?=\?)[^()]*\))*[^()]*\))/'.Modifiers::UTF8.Modifiers::DOTALL;
		// preg_match_all($pattern, $regex, $matches);
		// $ms = print_r($matches, true);
		// $ms = str_replace(["\n"], ["\n  "], $ms);
		// echo "\n>>>\n Captured Group: {$ms}";

	}

	/**
	 * Get the raw regex string from the given value. If given regex is a string,
	 * It's returned as it is.
	 *
	 * @internal
	 *
	 * @param  mixed $regex
	 * @return string
	 */
	protected function getLiteralFromRegex($regex)
	{
		if($regex instanceof self)
			return $regex->getLiteral();
		else
			return (string) $regex;
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
		$modifiers = is_none_string_iterable($modifiers)
				? $modifiers : str_split((string) $modifiers);

		foreach ($modifiers as $modifier) {
			if(strpos($this->modifiers, $modifier) === false)
				$this->modifiers .= $modifier;
		}
		return $this;
	}

	/**
	 * Add the given modifier to the regex.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param string $modifier
	 * @return $this
	 */
	public function modifier($modifier)
	{
		if(strpos($this->modifiers, $modifier) === false)
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
	public function remeveModifier($modifier)
	{
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
	public function remeveModifiers($modifiers)
	{
		$this->modifiers = str_replace($modifiers, '', $this->modifiers);
		return $this;
	}

	/**
	 * Determine whether the regex has any of the given modifiers.
	 *
	 * @see \Tea\Regex\Modifiers  For possible modifiers.
	 *
	 * @param  string   $modifier
	 * @return bool
	 */
	public function hasModifier($modifiers)
	{
		$modifiers = is_none_string_iterable($modifiers)
				? $modifiers : str_split((string) $modifiers);

		foreach ($modifiers as $modifier) {
			if(strpos($this->modifiers, $modifier) !== false)
				return true;
		}
		return false;
	}

	/**
	 * Get a string of all the set modifiers.
	 *
	 * @return string
	 */
	public function getModifiers()
	{
		return join('', $this->getModifiersArray());
	}

	/**
	 * Get an array of all the set modifiers.
	 *
	 * @return array
	 */
	public function getModifiersArray()
	{
		return array_filter(array_values($this->modifiers));
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
			return $this->modifiers(Modifiers::CASELESS);
		else
			return $this->remeveModifiers(Modifiers::CASELESS);
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
			return $this->modifiers(Modifiers::MULTILINE);
		else
			return $this->remeveModifiers(Modifiers::MULTILINE);
	}

	public function pregMatchFlags($flags)
	{
		$this->_pregMatchFlags = $flags;

		return $this;
	}

	public function startOfInput()
	{
		$this->_literal[] = "(?:^)";

		return $this;
	}

	public function startOfLine()
	{
		$this->multiLine();

		return $this->startOfInput();
	}

	public function endOfInput()
	{
		$this->flushState();
		$this->_literal[] = "(?:$)";

		return $this;
	}

	public function endOfLine()
	{
		$this->multiLine();

		return $this->endOfInput();
	}

	public function eitherFind($r)
	{
		if (is_string($r)) {
			return $this->setEither($this->getNew()->exactly(1)->of($r));
		}

		return $this->setEither($r);
	}


	protected function setEither(Builder $r)
	{
		$this->flushState();
		$this->_either = $this->combineGroupNumberingAndGetLiteral($r);

		return $this;
	}

	public function orFind(Builder $r)
	{
		if (is_string($r)) {
			return $this->setOr($this->getNew()->exactly(1)->of($r));
		}

		return $this->setOr($r);
	}

	public function anyOf(array $r)
	{
		if (count($r) < 1) {
			return $this;
		}

		$firstToken = array_shift($r);
		$this->eitherFind($firstToken);

		foreach ($r as $token) {
			$this->orFind($token);
		}

		return $this;
	}

	protected function setOr($r)
	{
		$either = $this->_either;
		$or     = $this->combineGroupNumberingAndGetLiteral($r);
		if ($either == "") {
			$lastOr = $this->_literal[count($this->_literal) - 1];

			$lastOr                                     = substr($lastOr, 0, (strlen($lastOr) - 1));
			$this->_literal[count($this->_literal) - 1] = $lastOr;
			$this->_literal[]                           = "|(?:" . $or . "))";
		} else {
			$this->_literal[] = "(?:(?:" . $either . ")|(?:" . $or . "))";
		}
		$this->clear();

		return $this;
	}


	public function neither($r)
	{

		if (is_string($r)) {
			return $this->notAhead($this->getNew()->exactly(1)->of($r));
		}

		return $this->notAhead($r);
	}

	public function nor($r)
	{
		if ($this->_min == 0 && $this->_ofAny) {
			$this->_min   = -1;
			$this->_ofAny = false;
		}
		$this->neither($r);

		return $this->min(0)->ofAny();
	}

	public function exactly($n)
	{
		$this->flushState();
		$this->_min = $n;
		$this->_max = $n;

		return $this;
	}

	public function min($n)
	{
		$this->flushState();
		$this->_min = $n;

		return $this;
	}

	public function max($n)
	{
		$this->flushState();
		$this->_max = $n;

		return $this;
	}

	/**
	 * Set the minimum and maximum quantifier. That is the min and max number
	 * of times a pattern will repeat. This similar to calling
	 * $this->min($min)->max($max).
	 * If max is not provided, $this->exactly($min) will be called.
	 *
	 * @see Builder::exactly()
	 * @see Builder::min()
	 * @see Builder::max()
	 * @param int  $min
	 * @param int  $max
	 * @return $this
	 */
	public function limit($min, $max = null)
	{
		if(is_null($max))
			return $this->exactly($min);


		$this->flushState();
		$this->_min = $min;
		$this->_max = $max;

		return $this;
	}

	public function of($s)
	{
		$this->_of = $this->sanitize($s);

		return $this;
	}


	public function ofAny()
	{
		$this->_ofAny = true;

		return $this;
	}

	public function ofGroup($n)
	{
		$this->_ofGroup = $n;

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

	public function like(Builder $r)
	{
		$this->_like = $this->combineGroupNumberingAndGetLiteral($r);

		return $this;
	}


	public function reluctantly()
	{
		$this->_reluctant = true;


		return $this;
	}


	public function ahead(Builder $r)
	{
		$this->flushState();
		$this->_literal[] = "(?=" . $this->combineGroupNumberingAndGetLiteral($r) . ")";

		return $this;
	}


	public function notAhead(Builder $r)
	{
		$this->flushState();
		$this->_literal[] = "(?!" . $this->combineGroupNumberingAndGetLiteral($r) . ")";

		return $this;
	}

	public function asGroup($name = null)
	{
		$this->_capture = true;
		$this->_captureName = $name;
		$this->_groupsUsed++;

		return $this;
	}

	/**
	 * @param $s
	 * @return $this
	 */
	public function then($s)
	{
		return $this->exactly(1)->of($s);
	}

	public function find($s)
	{
		return $this->then($s);
	}

	public function some($s)
	{
		return $this->min(1)->from($s);
	}

	public function maybeSome($s)
	{
		return $this->min(0)->from($s);
	}

	public function maybe($s)
	{
		return $this->max(1)->of($s);
	}

	public function anything()
	{
		return $this->min(0)->ofAny();
	}

	public function anythingBut($s)
	{
		if (strlen($s) === 1) {
			return $this->min(1)->notFrom(array($s));
		}
		$this->notAhead($this->getNew()->exactly(1)->of($s));

		return $this->min(0)->ofAny();
	}

	public function something()
	{
		return $this->min(1)->ofAny();
	}

	/**
	 * @return $this
	 */
	public function any()
	{
		return $this->exactly(1)->ofAny();
	}

	public function lineBreak()
	{
		$this->flushState();
		$this->_literal[] = "(?:\R)";

		return $this;
	}

	public function lineBreaks()
	{
		return $this->like($this->getNew()->lineBreak());
	}


	public function whitespace()
	{
		if ($this->_min == -1 && $this->_max == -1) {
			$this->flushState();
			$this->_literal[] = "(?:\\s)";

			return $this;
		}
		$this->_like = "\\s";

		return $this;
	}

	public function notWhitespace()
	{
		if ($this->_min == -1 && $this->_max == -1) {
			$this->flushState();
			$this->_literal[] = "(?:\\S)";

			return $this;
		}
		$this->_like = "\\S";

		return $this;
	}

	public function tab()
	{
		$this->flushState();
		$this->_literal[] = "(?:\\t)";

		return $this;
	}

	public function tabs()
	{
		return $this->like($this->getNew()->tab());
	}

	public function digit()
	{
		$this->flushState();
		$this->_literal[] = "(?:\\d)";

		return $this;
	}


	public function notDigit()
	{
		$this->flushState();
		$this->_literal[] = "(?:\\D)";

		return $this;
	}

	public function digits()
	{

		return $this->like($this->getNew()->digit());
	}

	public function notDigits()
	{
		return $this->like($this->getNew()->notDigit());
	}

	public function letter()
	{
		$this->exactly(1);
		$this->_from = "A-Za-z";

		return $this;
	}

	public function notLetter()
	{
		$this->exactly(1);
		$this->_notFrom = "A-Za-z";

		return $this;
	}

	public function letters()
	{
		$this->_from = "A-Za-z";

		return $this;
	}

	public function notLetters()
	{
		$this->_notFrom = "A-Za-z";

		return $this;
	}

	public function lowerCaseLetter()
	{
		$this->exactly(1);
		$this->_from = "a-z";

		return $this;
	}

	public function lowerCaseLetters()
	{
		$this->_from = "a-z";

		return $this;
	}

	public function upperCaseLetter()
	{
		$this->exactly(1);
		$this->_from = "A-Z";

		return $this;
	}

	public function upperCaseLetters()
	{
		$this->_from = "A-Z";

		return $this;
	}

	/**
	 * Append a pattern to the end.
	 *
	 * @todo   Add flags to provide options on what will be parsed.
	 *
	 * @param  Tea\Regex\Builder|string  $regex
	 * @param  int  $flags
	 * @return $this
	 */
	public function append($regex, $flags = 0)
	{
		if(!$regex instanceof self)
			$regex = static::parsePattern($regex)['literal'];

		$this->exactly(1);
		$this->_like = $this->combineGroupNumberingAndGetLiteral($regex);
		return $this;
	}

	public function optional($r)
	{
		$this->max(1);
		$this->_like = $this->combineGroupNumberingAndGetLiteral($r);

		return $this;
	}

	/**
	 * Parse the given raw expression and extracting the expression's body and
	 * modifiers.
	 *
	 * @todo   Add flags to provide options on what will be extracted.
	 * @internal
	 *
	 * @param  Tea\Regex\Builder|string  $regex
	 * @param  int  $flags
	 * @return array
	 */
	public static function parsePattern($regex, $flags = 0)
	{
		static $pattern = '/^([\/\~\#\%\+]{0,1})(?P<literal>.+)\1(?P<modifiers>[uimsxADSUXJ]*)$/us';

		if(false === preg_match($pattern, $regex, $matches))
			throw new InvalidArgumentException("Unable to parse raw regular expression '{$regex}'.");

		$components = ['literal' => '', 'modifiers' => ''];
		return array_intersect_key(array_merge($components, $matches), $components);

	}

	/**
	 * Quote/escape regular expression characters in given value.
	 *
	 * @param  string   $value
	 * @return string
	 */
	protected function sanitize($value)
	{
		return preg_quote($value, $this->delimiter);
	}

	/**
	 * Create a new builder instance. Accepts an optional pattern from which
	 * the builder can be created. The pattern can be another Builder instance
	 * or a raw regex string. The modifiers of the current instance will be set
	 * on the new instance.
	 * Throws a InvalidRegexPatternException if the given pattern is not a Builder
	 * instance and can't be converted to string.
	 *
	 * @param  string|\Tea\Regex\Builder|null   $pattern
	 * @return \Tea\Regex\Builder
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public function getNew($pattern = null)
	{
		return static::build($pattern)->modifiers($this->modifiers);
	}

	/**
	 * Create a new Builder instance. Accepts an optional pattern from which
	 * the builder can be created. The pattern can be another Builder instance
	 * or a raw regex string.
	 * Throws a InvalidRegexPatternException if the given pattern is not a Builder
	 * instance and can't be converted to string.
	 *
	 * @param  string|\Tea\Regex\Builder|null   $pattern
	 * @return \Tea\Regex\Builder
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public static function build($pattern = null)
	{
		return new static($pattern);
	}

	/**
	 * Cast a value into a Builder instance. If the value is already a Builder
	 * instance, it will be returned as it is. Otherwise will attempt to create
	 * a new Builder instance from the value as the pattern.
	 * Throws a InvalidRegexPatternException if the given pattern is not a Builder
	 * instance and can't be converted to string.
	 *
	 * @param  mixed   $pattern
	 * @return \Tea\Regex\Builder
	 *
	 * @throws \Tea\Regex\Exception\InvalidRegexPatternException
	 */
	public static function cast($pattern)
	{
		return $pattern instanceof self ? $pattern : new static($pattern);
	}

}