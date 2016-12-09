<?php
namespace Tea\Regex\Exception;

use UnexpectedValueException;

class InvalidRegexPatternException extends UnexpectedValueException
{
	public function __construct($value = '', $message = null, $code = 0, $previous = null)
	{
		if(is_null($message)){
			$value = is_object($value) ? "'".get_class($value)."' " : "'".strtoupper(gettype($value))."' ";
			$message = "Expects a string or an object which implements the __toString() method. {$value} given.";
		}
		parent::__construct($message, $code, $previous);
	}
}