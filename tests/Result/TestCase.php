<?php
namespace Tea\Regex\Tests\Result;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	public function emptyTest($test)
	{
		echo "\nTest: {$test} is empty.\n";
	}
}