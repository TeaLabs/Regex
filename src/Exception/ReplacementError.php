<?php
namespace Tea\Regex\Exception;

use Exception;

class ReplacementError extends RegexError
{
	public static function create($pattern, $subject, $message)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		return new static("Error replacing pattern `{$pattern}` in subject `{$subject}`. {$message}");
	}

}
