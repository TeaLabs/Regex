<?php
namespace Tea\Regex;


/**
* A wrapper for PREG_* flags.
*/
class Flags
{
	/**
	 * @var int
	 */
	const OFFSET_CAPTURE = PREG_OFFSET_CAPTURE;

	/**
	 * @var int
	 */
	const PATTERN_ORDER = PREG_PATTERN_ORDER;

	/**
	 * @var int
	 */
	const SET_ORDER = PREG_SET_ORDER;

	/**
	 * @var int
	 */
	const GREP_INVERT = PREG_GREP_INVERT;

	/**
	 * @var int
	 */
	const FILTER_INVERT = PREG_GREP_INVERT;

	/**
	 * @var int
	 */
	const SPLIT_NO_EMPTY = PREG_SPLIT_NO_EMPTY;

	/**
	 * @var int
	 */
	const SPLIT_DELIM_CAPTURE = PREG_SPLIT_DELIM_CAPTURE;

	/**
	 * @var int
	 */
	const SPLIT_OFFSET_CAPTURE = PREG_SPLIT_OFFSET_CAPTURE;

}