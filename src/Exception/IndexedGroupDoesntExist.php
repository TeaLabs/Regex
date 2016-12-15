<?php
namespace Tea\Regex\Exception;

use Exception;

class IndexedGroupDoesntExist extends RegexError
{
	public static function create($pattern, $subject, $index)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group at index {$index}");
	}

}
