<?php
namespace Tea\Regex;


use Tea\Regex\Exception\InvalidModifierException;

/**
* Human readable modifiers and their values.
*
* @see http://php.net/manual/en/reference.pcre.pattern.modifiers.php For more possible modifiers.
*/
class Modifiers
{

	/**
	 * @var string
	 */
	const UTF8           = 'u';

	/**
	 * @var string
	 */
	const CASELESS       = 'i';

	/**
	 * @var string
	 */
	const MULTILINE      = 'm';

	/**
	 * @var string
	 */
	const DOTALL         = 's';

	/**
	 * @var string
	 */
	const EXTENDED       = 'x';

	/**
	 * @var string
	 */
	const ANCHORED       = 'A';

	/**
	 * @var string
	 */
	const DOLLAR_ENDONLY = 'D';

	/**
	 * @var string
	 */
	const STUDY          = 'S';

	/**
	 * @var string
	 */
	const UNGREEDY       = 'U';

	/**
	 * @var string
	 */
	const EXTRA          = 'X';

	/**
	 * @var string
	 */
	const INFO_JCHANGED  = 'J';


	/**
	 * All allowed modifiers.
	 *
	 * @var string
	 */
	const ALL = 'uimsxADSUXJ';


	/**
	 * @var array
	 */
	protected static $asciiMap = [
			'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
				'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
				'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
				'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი',
				'इ', 'ی'],
			'm' => ['м', 'μ', 'م', 'မ', 'მ'],
			's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს'],
			'x' => ['χ', 'ξ'],
			'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
				'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą',
				'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ',
				'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ',
				'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ'],
			'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
			'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
			'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
				'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ',
				'Ǘ', 'Ǚ', 'Ǜ'],
			'X' => ['Χ', 'Ξ'],
			'J' => ['Ĵ', 'ĵ', 'ј', 'Ј', 'ჯ', 'ج'],
			'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
				'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ',
				'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ'
				]
		];

	/**
	 * @var array
	 */
	protected static $asciiMapCompiled;

	/**
	 * Returns an ASCII version of the given modifier. If the modifier is a
	 * non-ASCII character it will be replaced with the closest ASCII counterpart.
	 * Otherwise it will be returned as it is.
	 *
	 * @todo Optimize this method.
	 *
	 * @param  string $modifiers
	 * @return string
	*/
	public static function toAscii($modifiers)
	{
		if(function_exists('mb_check_encoding') && mb_check_encoding($modifiers, 'ASCII'))
			return $modifiers;

		$map = static::compiledAsciiMap();
		return preg_replace($map['search'], $map['replace'], $modifiers);
	}

	protected static function compiledAsciiMap()
	{
		if(is_null(static::$asciiMapCompiled)){
			static::$asciiMapCompiled = ['search' => [], 'replace' => []];
			foreach (static::$asciiMap as $ascii => $values) {
				static::$asciiMapCompiled['search'][] = '/['.join('', $values).']+/u';
				static::$asciiMapCompiled['replace'][] = $ascii;
			}
		}

		return static::$asciiMapCompiled;
	}

	/**
	 * Determine if the given value consists of only valid modifiers. Returns
	 * FALSE if the string contains invalid modifier character(s).
	 *
	 * @param  string $modifiers
	 * @return bool
	*/
	public static function isValid($modifiers)
	{
		return static::validate($modifiers, true);
	}

	/**
	 * Determine if the given value consists of only valid modifiers. Unless
	 * silent is set to TRUE, an InvalidModifierException is thrown in the case
	 * of an invalid modifier. Returns false otherwise.
	 *
	 * @param  string $modifiers
	 * @param  bool   $silent
	 * @return bool
	 *
	 * @throws \Tea\Regex\Exception\InvalidModifierException
	*/
	public static function validate($modifiers, $silent = false)
	{
		if(0 === preg_match('/([^'.self::ALL.'])/u', $modifiers))
			return true;

		if($silent)	return false;

		throw new InvalidModifierException($modifiers);
	}
}