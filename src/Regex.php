<?php
namespace Tea\Regex;

use TypeError;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

/**
*
*/
class Regex
{

	/**
	 * @var array
	*/
	protected static $methodArgs;


	/**
	 * Create a Builder instance.
	 *
	 * @see  Tea\Regex\Builder::__construct()
	 *
	 * @param  string|null  $delimiter
	 * @param  string|null  $modifiers
	 *
	 * @return Tea\Regex\Builder
	 */
	public static function builder($delimiter = null, $modifiers = null)
	{
		return new Builder($delimiter, $modifiers);
	}


	/**
	 * Create a RegularExpression instance.
	 * If either the modifiers and/or the delimiter are not provided, the defaults
	 * {@see \Tea\Regex\Config} will be used.
	 *
	 * @see  \Tea\Regex\RegularExpression::create()
	 *
	 * @param  string              $body
	 * @param  string|null|false   $modifiers
	 * @param  string|null         $delimiter
	 *
	 * @return \Tea\Regex\RegularExpression
	 */
	public static function create($body, $modifiers = null, $delimiter = null)
	{
		if(is_array($body))
			return RegularExpressionCollection::create($body, $modifiers, $delimiter);
		else
			return RegularExpression::create($body, $modifiers, $delimiter);
	}

	/**
	 * Create a RegularExpression instance from a possibly complete regex string
	 * or a {@see \Tea\Regex\Contracts\Pattern} instance.
	 * If the given pattern is string it will be parsed to extract the regex body,
	 * modifiers and the delimiter if any.
	 *
	 * If either the modifiers and/or delimiter are neither set on the pattern
	 * nor passed as arguments, the defaults {@see \Tea\Regex\Config} will be used.
	 * Modifiers and/or the delimiter passed as arguments will be used instead
	 * of those set on the pattern.
	 *
	 * @see  \Tea\Regex\RegularExpression::from()
	 *
	 * @param  string|\Tea\Regex\Contracts\Pattern  $pattern
	 * @param  string|null|false                    $modifiers
	 * @param  string|null                          $delimiter
	 *
	 * @return \Tea\Regex\RegularExpression
	 */
	public static function from($pattern, $modifiers = null, $delimiter = null)
	{
		return RegularExpression::from($pattern, $modifiers, $delimiter);
	}

	public static function quote($value, $delimiter = null)
	{
		return Adapter::quote($value, $delimiter);
	}

	/**
	 * Creates an instance of RegularExpression from the first argument and
	 * invokes the given method with the rest of the passed arguments. The
	 * optional modifiers and delimiter are expected to be the last arguments.
	 *
	 * For example, the following:
	 *   Regex::match('( \d+ )', 'foo25', 0, 0, 'ux', '#');
	 * translates to
	 *   RegularExpression::from('( \d+ )', 'ux', '#')->match('foo25', 0, 0);
	 *
	 * @param string  $name
	 * @param array $arguments
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($name, $arguments)
	{
		if (is_null(static::$methodArgs)) {
			$regexClass = new ReflectionClass('Tea\Regex\RegularExpression');
			$methods = $regexClass->getMethods(ReflectionMethod::IS_PUBLIC);

			foreach ($methods as $method) {
				static::$methodArgs[$method->name] = $method->isStatic()
						? false : $method->getNumberOfParameters() + 3;
			}
		}

		if (!isset(static::$methodArgs[$name])){
			throw new BadMethodCallException("Call to unknown RegularExpression method '{$name}'.");
		}

		$params = static::$methodArgs[$name];

		if($params === false){
			return call_user_func_array(['Tea\Regex\RegularExpression', $name], $arguments);
		}

		$numArgs = count($arguments);

		if($numArgs < 1){
			throw new TypeError("At least 1 argument (the regex pattern),"
				." is required to create a RegularExpression instance in order"
				." to invoke method '{$name}()'.");
		}

		$pattern = $arguments[0];

		if ($numArgs === $params){
			$args = array_slice($arguments, 1, -2);
			$modifiers = $arguments[$numArgs - 2];
			$delimiter = $arguments[$numArgs - 1];
		}
		elseif($numArgs === ($params - 1)){
			$args = array_slice($arguments, 1, -1);
			$modifiers = $arguments[$numArgs - 1];
			$delimiter = null;
		}
		else {
			$args = array_slice($arguments, 1);
			$modifiers = null;
			$delimiter = null;
		}

		$instance = static::from($pattern, $modifiers, $delimiter);

		return call_user_func_array(array($instance, $name), $args);
	}
}