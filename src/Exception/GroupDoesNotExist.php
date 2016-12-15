<?php
namespace Tea\Regex\Exception;

use Exception;

class GroupDoesNotExist extends RegexError
{
	public static function create($pattern, $subject, $index)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		$index = static::formatObject($index);
		return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture group index {$index}.");
	}

}
