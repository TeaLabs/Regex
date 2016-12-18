<?php
namespace Tea\Regex\Exception;

use Exception;

class FilterError extends RegexError
{
	public static function create($pattern, $input, $message)
	{
		$pattern = static::formatObject($pattern);
		$input = static::formatObject($input);

		return new static("Error filtering input {$input} with pattern {$pattern}. {$message}");
	}

}
