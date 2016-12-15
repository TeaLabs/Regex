<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Config;
use Tea\Regex\Regex;

class ConfigTest extends TestCase
{
	protected static $ran = false;

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


	public function rawProvider()
	{
		return [
			[ [], '/^abc.+xyz$/ui' ],
			[ [], '^abc.+xyz$' ],
			[ [], 'xxẦ' ],
			[ [], '' ],
			[ [], '/^(abc(?:xx))(\s).+(\d)(?:skp)(\1)$/u' ],
			[[], "Call 555-1212 or 1-800-555-1212", "/([a-zA-Z]*)\(?  (\d{3})?  \)?  (?(1)  [\-\s] ) \d{3}-\d{4}/x"],
			[[], "Call 555-1212 or 1-800-555-1212",
				"/ \s* ([a-zA-Z]*) \s* (?P<phone> \d{0,1}\-{0,1} (?:\d\d\d\-){1,2}\d{4} ) /x"],
			[[], "Call 555-1212 or 1-800-555-1212",
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"],
			[[], "Call 555-1212 or 1-800-555-1212",
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"],
			[[], "Call 555-1212 or 1-800-555-1212",
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"],
			// [[], "Call 555-1212 or 1-800-555-1212", "/ \s* (?P<label>[a-zA-Z]*) \s* (?P<phone> \d{0,1}\-{0,1} (?:\d\d\d\-){1,2}\d{4} ) /x"],
			// [[], "Call 555-1212 or 1-800-555-1212 Home",
			// 		"/ \s* ([a-zA-Z]*) \s* (\d{0,1}\-{0,1} (?:\d\d\d\-){1,2} \d{4} ) \s* (?:([a-zA-Z]*\s*$)) /x"],
		];
	}

	/**
	 * @dataProvider rawProvider()
	 */
	public function testRaw($expected, $pattern, $regex = null, $flags = 0, $builder = null)
	{
		// static $_regex = '/^([\/\~\#\%\+]{0,1})(?P<literal>.+)\1(?P<modifiers>[uimsxADSUXJ]*)$/us';
		static $_regex = '/^([\/\~\#\%\+]{0,1})(.+)\1([uimsxADSUXJ]*)$/us';

		$regex = $regex ?: $_regex;

		$jsflags = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
		$matches_all = new \ArrayObject();
		$result = json_encode(preg_match($regex, $pattern, $matches), $jsflags);

		$result_all = json_encode(preg_match_all($regex, $pattern, $matches_all), $jsflags);

		$result_all_po = json_encode(preg_match_all($regex, $pattern, $matches_all_po, PREG_PATTERN_ORDER), $jsflags);

		$this->assertEquals($matches_all, $matches_all_po);

		$matches = json_encode($matches, $jsflags | JSON_FORCE_OBJECT);
		$matches_all = json_encode($matches_all, $jsflags | JSON_FORCE_OBJECT);
		$pattern = json_encode($pattern, $jsflags);

		$regex = json_encode($regex, $jsflags);
		$ln = str_repeat('-', 100);
		if(!static::$ran){
			echo "\n{$ln}";
			static::$ran = true;
		}
		echo "\n Pattern: {$pattern}\n Regex: {$regex}\n Match #{$result}: {$matches}\n Match All #{$result_all}: {$matches_all}\n{$ln}";
	}

	/**
	 * @dataProvider rawProvider()
	 */
	public function _testRawResults($expected, $pattern, $regex = null, $flags = 0, $builder = null)
	{
		// static $_regex = '/^([\/\~\#\%\+]{0,1})(?P<literal>.+)\1(?P<modifiers>[uimsxADSUXJ]*)$/us';
		static $_regex = '/^([\/\~\#\%\+]{0,1})(.+)\1([uimsxADSUXJ]*)$/us';

		$regex = $regex ?: $_regex;

		$jsflags = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

		$result = json_encode(Regex::match($regex, $pattern), $jsflags);
		$result_all = json_encode(Regex::matchAll($regex, $pattern), $jsflags);
		$matches = null; //json_encode($matches, $jsflags | JSON_FORCE_OBJECT);
		$matches_all = null;//json_encode($matches_all, $jsflags | JSON_FORCE_OBJECT);
		$pattern = json_encode($pattern, $jsflags);
		$regex = json_encode($regex, $jsflags);
		$ln = str_repeat('-', 100);
		if(!static::$ran){
			echo "\n{$ln}";
			static::$ran = true;
		}
		echo "\n Pattern: {$pattern}\n Regex: {$regex}\n Match #{$result}: {$matches}\n Match All #{$result_all}: {$matches_all}\n{$ln}";
	}


}