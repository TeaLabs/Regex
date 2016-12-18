<?php
namespace Tea\Regex\Exception;

use Exception;

class IllegalReplacementTypeAccess extends RegexError
{
	public static function create($pattern, $subject, $illegalType, $actualType)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		return new static("`{$actualType}` replacement result for pattern `{$pattern}` ".
			"in subject `{$subject}` can't be accessed as `{$illegalType}`.");
	}

	public static function arrayAsString($pattern, $subject)
	{
		return static::create($pattern, $subject, 'String', 'Array');
	}

	public static function stringAsArray($pattern, $subject)
	{
		return static::create($pattern, $subject, 'Array', 'String');
	}
}
