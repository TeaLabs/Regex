<?php
namespace Tea\Regex\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	protected static $_empties = 1;
	protected static $_emptyCases = [];

	public function emptyTest($test)
	{
		if(!isset(static::$_emptyCases[get_class($this)])){
			static::$_emptyCases[get_class($this)] = true;
			echo "\n";
		}
		$count = static::$_empties;

		echo "\n Empty test {$count} - {$test}()\n";
		static::$_empties ++;
	}


	/**
	 * Asserts that a variable is of a RegularExpression instance.
	 *
	 * @param mixed $object
	 */
	public function assertIsRegularExpression($object)
	{
		$this->assertInstanceOf('Tea\Regex\RegularExpression', $object);
	}

	/**
	 * Asserts that a variable is of a Matches instance.
	 *
	 * @param mixed $object
	 */
	public function assertInstanceOfMatches($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Matches', $object);
	}


	/**
	 * Asserts that a variable is of a Replacement instance.
	 *
	 * @param mixed $object
	 */
	public function assertInstanceOfReplacement($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Replacement', $object);
	}


	public function assertIsBuilder($object)
	{
		$this->assertInstanceOf('Tea\Regex\Builder', $object);
	}

}