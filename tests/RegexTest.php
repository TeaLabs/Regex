<?php
namespace Tea\Tests\Regex;

use Tea\Regex\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{


	public function delimiterProvider()
	{
		return [
			[Regex::DEFAULT_DELIMITER, null],
			['/', '/'],
			['+', '+'],
			['#', '#'],
		];
	}

	/**
	 * @dataProvider delimiterProvider()
	 */
	public function testDelimiter($expected, $new= null)
	{
		$old = Regex::delimiter();
		Regex::delimiter($new);
		$actual = Regex::delimiter();
		$reset = Regex::delimiter($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}

	public function matchProvider()
	{
		return [
			[['defऑ'], '/defऑ$/u', 'abcdefऑ'],
			[[], '/defऑ$/u', 'abcdef']
		];
	}

	/**
	 * @dataProvider matchProvider()
	 */
	public function testMatch($expected, $pattern, $subject, $flags =0, $offset = 0)
	{
		$actual = Regex::match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $actual);
	}

	public function modifiersProvider()
	{
		return [
			[Regex::DEFAULT_MODIFIERS, null],
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
		$old = Regex::modifiers();
		Regex::modifiers($new);
		$actual = Regex::modifiers();
		$reset = Regex::modifiers($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}

	public function quoteProvider()
	{
		return [
			[null, null],
			[null, null, '/'],
			['', '', '/'],
			['\[x\\'.Regex::delimiter().'z\]', '[x'.Regex::delimiter().'z]'],
			['\[x\/z\]', '[x/z]', '/'],
			['\[x\/z\]', '[x/z]', null, '/'],
			['\[x/z\]', '[x/z]', false, '/'],
		];
	}

	/**
	 * @dataProvider quoteProvider()
	 */
	public function testQuote($expected, $string = null, $delimiter = null, $globalDelimiter = null)
	{
		$origDelimiter = Regex::delimiter();
		Regex::delimiter($globalDelimiter);
		$actual = Regex::quote($string, $delimiter);
		Regex::delimiter($origDelimiter);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($origDelimiter, Regex::delimiter());
	}

	public function wrapProvider()
	{
		$re = '([a-zA-Z_][a-zA-Z0-9_-]*|)';
		$reb = "\\{$re}\\";
		return [
			[ "/{$re}/u", "{$re}"],
			[ "+{$re}+", "+{$re}+"],
			[ "/{$re}/im", "/{$re}/im"],
			[ "#{$re}#im", "{$re}", '#', 'im'],
			[ "#{$re}#im", "#{$re}#im", '#', 'im'],
			[ "~{$re}~iADJ", "~{$re}~iADJ"],
			[ "+{$re}+iADJ", "+{$re}+iADJ"],
			[ "%{$re}%iADJ", "%{$re}%iADJ"],
			[ "[{$re}]iADJ", "[{$re}]iADJ", null, null,true],
			[ "({$re})iADJ", "({$re})iADJ", null, null,true],
			[ "<{$re}>iADJ", "<{$re}>iADJ", null, null,true],
			[ "{{$re}}iADJ", "{{$re}}iADJ", null, null,true],
			[ "{{$reb}}u", "{$reb}", '{}', null,true],
			[ "{{$re}}u", "{$re}", '{}', null, ['<{[','>}]']],
			[ "<{$reb}>i", "$reb", '<>', 'i', true],
			[ "<{$re}>i", "$re", '<>', 'i', ['<{[','>}]']],
		];
	}

	/**
	 * @dataProvider wrapProvider()
	 */
	public function _testWrap($expected, $regex, $delimiter = null, $modifiers = null, $bracketStyle = false)
	{
		for ($i=0; $i < 1; $i++) {
			$actual = Regex::wrap($regex, $delimiter, $modifiers, $bracketStyle);
		}

		$this->assertEquals($expected, $actual);
	}

}