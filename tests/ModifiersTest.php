<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Modifiers;

class ModifiersTest extends TestCase
{

	public function toAsciiProvider()
	{
		return [
			['s', 's'],
			['s', 'š'],
			['uix', 'uix'],
			['uix', 'ùìx'],
			['ui', 'ǜùì']
		];
	}

	/**
	 * @dataProvider toAsciiProvider()
	 */
	public function testToAscii($expected, $modifiers)
	{
		$revs = 1;

		for ($i=0; $i < $revs; $i++) {
			$actual = Modifiers::toAscii($modifiers);
		}

		$this->assertEquals($expected, $actual);
	}

	public function isValidProvider()
	{
		return [
			[true, Modifiers::CASELESS.Modifiers::MULTILINE.Modifiers::DOTALL],
			[true, Modifiers::ALL],
			[false, 'qwerty'],
			[false, Modifiers::CASELESS.Modifiers::UTF8.'t'],
			[false, '1'.Modifiers::CASELESS.Modifiers::UTF8],
		];
	}

	/**
	 * @dataProvider isValidProvider()
	 */
	public function testIsValid($expected, $value)
	{
		$revs = 1;

		for ($i=0; $i < $revs; $i++) {
			$actual = Modifiers::isValid($value);
		}

		$this->assertEquals($expected, $actual);
	}

	public function validateProvider()
	{
		return [
			[true, Modifiers::CASELESS.Modifiers::MULTILINE.Modifiers::DOTALL],
			[true, Modifiers::ALL],
			[false, 'qwerty'],
			[false, Modifiers::CASELESS.Modifiers::UTF8.'t'],
			[false, '1'.Modifiers::CASELESS.Modifiers::UTF8],
		];
	}

	/**
	 * @dataProvider validateProvider()
	 */
	public function testValidate($expected, $value, $silent = true)
	{
		$revs = 1;

		for ($i=0; $i < $revs; $i++) {
			$actual = Modifiers::validate($value, $silent);
		}

		$this->assertEquals($expected, $actual);
	}

	public function validateThrowsInvalidModifierExceptionProvider()
	{
		return [
			['qwerty'],
			[Modifiers::CASELESS.Modifiers::UTF8.'t'],
			['1'.Modifiers::CASELESS.Modifiers::UTF8],
		];
	}

	/**
	 * @dataProvider validateThrowsInvalidModifierExceptionProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidModifierException
	 */
	public function testValidateThrowsInvalidModifierException($value)
	{
		$actual = Modifiers::validate($value);
		$this->fail('Expecting error when the value contains invalid modifiers.');

	}
}