<?php
namespace Tea\Regex;


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
	return RegularExpression::create($body, $modifiers, $delimiter);
}
