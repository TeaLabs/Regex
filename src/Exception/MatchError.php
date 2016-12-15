<?php
namespace Tea\Regex\Exception;

use Exception;

class MatchError extends RegexError
{
	public static function create($pattern, $subject, $message)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);

		return new static("Error matching pattern `{$pattern}` with subject `{$subject}`. {$message}");
	}

}
