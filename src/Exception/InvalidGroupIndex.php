<?php
namespace Tea\Regex\Exception;

use Exception;

class InvalidGroupIndex extends RegexError
{
	public static function create($pattern, $subject, $index, $message = "String, Integer or Iterable expected.")
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		$index = static::formatObject($index);
		return new static("Invalid group index {$index} for matched pattern `{$pattern}` with subject `{$subject}`. {$message}");
	}

}
