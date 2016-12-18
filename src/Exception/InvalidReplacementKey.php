<?php
namespace Tea\Regex\Exception;

use Exception;

class InvalidReplacementKey extends RegexError
{
	public static function create($pattern, $subject, $key)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		$key = static::formatObject($key);
		return new static("Invalid replacement result key {$key} for pattern `{$pattern}` in subject `{$subject}`.");
	}

}
