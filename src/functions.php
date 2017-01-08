<?php
namespace Tea\Regex;

use Tea\Regex\Utils\Helpers;

/**
 * Create a RegularExpression instance. If either the modifiers and/or the
 * delimiter are not provided, the defaults {@see \Tea\Regex\Config} will be used.
 *
 * @see  \Tea\Regex\RegularExpression::create()
 *
 * @param  string              $body
 * @param  string|null|false   $modifiers
 * @param  string|null         $delimiter
 *
 * @return \Tea\Regex\RegularExpression
 */
function re($body, $modifiers = null, $delimiter = null)
{
	return Regex::create($body, $modifiers, $delimiter);
}

/**
 * Create a RegularExpression instance from a possibly complete regex string
 * or a {@see \Tea\Regex\Contracts\Pattern} instance.
 * If the given pattern is string it will be parsed to extract the regex body,
 * modifiers and the delimiter if any.
 *
 * If either the modifiers and/or delimiter are neither set on the pattern
 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
 * Modifiers and/or the delimiter passed as arguments will be used instead
 * of those set on the pattern.
 *
 * @see  \Tea\Regex\RegularExpression::from()
 *
 * @param  string|\Tea\Regex\Contracts\Pattern  $pattern
 * @param  string|null|false                    $modifiers
 * @param  string|null                          $delimiter
 *
 * @return \Tea\Regex\RegularExpression
 */
function re_from($pattern, $modifiers = null, $delimiter = null)
{
	return Regex::from($pattern, $modifiers, $delimiter);
}