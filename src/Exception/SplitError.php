<?php
namespace Tea\Regex\Exception;

use Exception;

class SplitError extends RegexError
{
	public static function create($pattern, $subject, $message)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		return new static("Error splitting string {$subject} using regex pattern {$pattern}. {$message}");
	}

}
