<?php
namespace Tea\Regex\Exception;

use Exception;

class UnknownReplacementKey extends RegexError
{
	public static function create($pattern, $subject, $key)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		$key = static::formatObject($key);
		return new static("Key {$key} does not exist in replacement result for pattern `{$pattern}` in subject `{$subject}`.");
	}

}
