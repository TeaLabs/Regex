<?php
namespace Tea\Regex\Exception;

use Tea\Regex\Utils\Helpers;
use UnexpectedValueException;

class InvalidModifierException extends UnexpectedValueException
{
	public function __construct($value = '', $message = null, $code = 0, $previous = null)
	{
		if(is_null($message)){
			if($value != ''){
				if(Helpers::isStringable($value))
					$value = "'{$value}' ";
				elseif(is_object($value))
					$value = "'".get_class($value)."' ";
				elseif(is_array($value))
					$value = "'".join("', '", $value)."' ";
			}
			$message = "Invalid modifier(s) {$value}. See Tea\Regex\Modifiers for possible modifiers.";
		}

		parent::__construct($message, $code, $previous);
	}
}