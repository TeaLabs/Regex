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

}