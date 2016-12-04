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

	public function safeWrapProvider()
	{
		$re = '([a-zA-Z_][a-zA-Z0-9_-]*|)';
		$reb = "\\{$re}\\";
		$dlm = Regex::delimiter();
		return [
			[ "{$dlm}{$re}{$dlm}", "{$re}"],
			[ "/{$re}/", "{$re}", '/'],
			[ "/{$re}/", "/{$re}/"],
			[ "+{$re}+", "+{$re}+"],
			[ "/{$re}/im", "/{$re}/im"],
			[ "#{$re}#", "{$re}", '#'],
			[ "#{$re}#im", "#{$re}#im", '#'],
			[ "~{$re}~iADJ", "~{$re}~iADJ"],
			[ "+{$re}+iADJ", "+{$re}+iADJ"],
			[ "%{$re}%iADJ", "%{$re}%iADJ"],
			[ "[{$re}]iADJ", "[{$re}]iADJ", null, true],
			[ "({$re})iADJ", "({$re})iADJ", null, true],
			[ "<{$re}>iADJ", "<{$re}>iADJ", null, true],
			[ "{{$re}}iADJ", "{{$re}}iADJ", null, true],
			[ "{{$reb}}", "{$reb}", '{}', true],
			[ "{{$re}}", "{$re}", '{}', ['<{[','>}]']],
			[ "<{$reb}>", "$reb", '<>', true],
			[ "<{$re}>", "$re", '<>', ['<{[','>}]']],
		];
	}

	/**
	 * @dataProvider safeWrapProvider()
	 */
	public function testSafeWrap($expected, $regex, $delimiter = null, $bracketStyle = false)
	{
		// for ($i=0; $i < $revs; $i++) {
		$actual = Regex::safeWrap($regex, $delimiter, $bracketStyle);
		// }
		$this->assertEquals($expected, $actual);
	}

	public function wrapProvider()
	{
		$dlmt = Regex::delimiter();
		return [
			[ $dlmt.'(.*)'.$dlmt, '(.*)'],
			[ $dlmt.'(.*)'.$dlmt, '(.*)', ''],
			[ '/(.*)/', '(.*)', '/'],
			[ '~(.*)~', '(.*)', '~'],
			[
				[$dlmt.'(1*)'.$dlmt, $dlmt.'(2*)'.$dlmt, $dlmt.'(3*)'.$dlmt],
				['(1*)', '(2*)', '(3*)']
			],
		];
	}

	/**
	 * @dataProvider wrapProvider()
	 */
	public function testWarp($expected, $regex, $delimiter = null)
	{
		$actual = Regex::wrap($regex, $delimiter);
		$this->assertEquals($expected, $actual);
	}

}