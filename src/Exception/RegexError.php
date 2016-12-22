<?php
namespace Tea\Regex\Exception;

use Exception;
use Tea\Regex\Utils\Helpers;
use BadMethodCallException;

class RegexError extends Exception
{
	protected static function trimString($string)
	{
		if (strlen($string) < 40) {
			return $string;
		}

		return substr($string, 0, 40).'...';
	}

	protected static function formatObject($object)
	{
		$type = is_object($object) ? get_class($object) : ucfirst(gettype($object));

		if(Helpers::isNoneStringIterable($object))
			$value = ' [' . static::trimString(Helpers::implodeIterable($object, false, null, '', '')) .']';
		elseif(Helpers::isStringable($object))
			$value = " " . static::trimString((string) $object);
		else
			$value = "";
		return "<{$type}{$value}>";
	}

	public static function __callStatic($method, $args)
	{
		if(strpos($method, 'create') === 0){
			$class = "Tea\\Regex\\Exception\\" . substr($method, 6);
			return call_user_func_array([$class, 'create'], $args);
		}

		throw new BadMethodCallException("Call to unknown static method {$method}.");
	}
}
