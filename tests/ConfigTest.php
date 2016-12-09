<?php
namespace Tea\Tests\Regex;

use Tea\Regex\Config;

class ConfigTest extends TestCase
{

	public function delimiterProvider()
	{
		return [
			[Config::delimiter(), null],
			[Config::delimiter(), ''],
			['/', '/'],
			['%', '%'],
			['#', '#'],
		];
	}

	/**
	 * @dataProvider delimiterProvider()
	 */
	public function testDelimiter($expected, $new= null)
	{
		$old = Config::delimiter();
		Config::delimiter($new);
		$actual = Config::delimiter();
		$reset = Config::delimiter($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}


	/**
	 * @expectedException \Tea\Regex\Exception\InvalidDelimiterException
	 */
	public function testDelimiterWithError()
	{
		$actual = Config::delimiter('xxx');
		$this->fail('Expecting error when the value contains invalid delimiter.');

	}

	public function modifiersProvider()
	{
		return [
			[Config::modifiers(), null],
			['ui', 'ui'],
			['uix', 'uix'],
			['', ''],
		];
	}

	/**
	 * @dataProvider modifiersProvider()
	 */
	public function testModifiers($expected, $new= null)
	{
		$old = Config::modifiers();
		Config::modifiers($new);
		$actual = Config::modifiers();
		$reset = Config::modifiers($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}


}