<?php
namespace Tea\Regex\Exception;

use Tea\Regex;
use UnexpectedValueException;

class InvalidDelimiterException extends UnexpectedValueException
{
	public function __construct($value = '', $message = null, $code = 0, $previous = null)
	{
		if(is_null($message)){
			if($value != ''){
				if(Regex\is_stringable($value))
					$value = "'{$value}' ";
				elseif(is_object($value))
					$value = "'".get_class($value)."' ";
				elseif(is_array($value))
					$value = "'".join("', '", $value)."' ";
			}
			$message = "Invalid delimiter {$value}. Expected '/', '~', '#' or '%'.";
		}
		parent::__construct($message, $code, $previous);
	}
}