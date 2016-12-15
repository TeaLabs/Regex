<?php
namespace Tea\Regex\Exception;

use Exception;

class NamedGroupDoesntExist extends RegexError
{
	public static function create($pattern, $subject, $groupName)
	{
		$pattern = static::formatObject($pattern);
		$subject = static::formatObject($subject);
		return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group named {$groupName}");
	}

}
