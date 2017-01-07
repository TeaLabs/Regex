<?php
namespace Tea\Regex\Tests\Result;

use Tea\Regex\Result\Matches;
use Tea\Regex\Result\OrderedMatches;
use Tea\Regex\Tests\Mocks\StringObject;

class OrderedMatchesTest extends TestCase
{

	protected function match($pattern, $subject, $flags = null, $offset = 0)
	{
		$flags = ((int) $flags) | PREG_SET_ORDER;
		$result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
		return new OrderedMatches($pattern, $subject, $matches, $result, $flags);
	}

	/**
	 * Asserts that a variable is of a Matches instance.
	 *
	 * @param mixed $object
	 */
	public function assertIsOrderedMatches($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\OrderedMatches', $object);
	}

	public function testCreate()
	{
		$pattern = '/^f/u';
		$subject = 'foo';
		$matches = $this->match($pattern, $subject);

		$this->assertIsOrderedMatches($matches);
		$this->assertEquals($pattern, $matches->pattern());
		$this->assertEquals($subject, $matches->subject());
	}


	public function allProvider()
	{
		return [
			[
				[
					new Matches(
						$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212",
						['555-1212', '555-1212'], true),
					new Matches($pattern, $subject, ['1-800-555-1212', '1-800-555-1212'], true),
				],
				$pattern,
				$subject,
			],
			[
				[
					new Matches(
						$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212",
						['Call 555-1212', '555-1212'], true),
					new Matches($pattern, $subject, [' or 1-800-555-1212', '1-800-555-1212'], true),
				],
				$pattern,
				$subject,
			],
			[
				[
					new Matches(
						$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212",
						['Call 555-1212', 'Call', '555-1212'], true),
					new Matches($pattern, $subject, [' or 1-800-555-1212', 'or', '1-800-555-1212'], true),
				],
				$pattern,
				$subject,
			],
		];
	}

	/**
	 * @dataProvider allProvider()
	 */
	public function testAll($expected, $pattern, $subject, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->all());
	}

	/**
	 * @dataProvider allProvider()
	 */
	public function testResult($expected, $pattern, $subject, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->result());
	}


	public function indexedGroupsProvider()
	{
		return [
			[
				[
					['555-1212', '555-1212'],
					['1-800-555-1212', '1-800-555-1212']
				],
				$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject = "Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['Call 555-1212', '555-1212'],
					[' or 1-800-555-1212', '1-800-555-1212']
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
			[
				[
					['Call 555-1212', 'Call', '555-1212'],
					[' or 1-800-555-1212', 'or', '1-800-555-1212']
				],
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
		];
	}

	/**
	 * @dataProvider indexedGroupsProvider()
	 */
	public function testIndexedGroups($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->indexedGroups());
	}

	public function namedGroupsProvider()
	{
		return [
			[
				[
					['phone' => '555-1212'],
					['phone' => '1-800-555-1212'],
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject = "Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['text' => 'Call', 'phone' => '555-1212'],
					['text' => 'or', 'phone' => '1-800-555-1212'],
				],
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
		];
	}

	/**
	 * @dataProvider namedGroupsProvider()
	 */
	public function testNamedGroups($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->namedGroups());
	}

	public function namedProvider()
	{
		return [
			[
				[
					['phone' => '555-1212'],
					['phone' => '1-800-555-1212'],
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject = "Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['text' => 'Call', 'phone' => '555-1212'],
					['text' => 'or', 'phone' => '1-800-555-1212'],
				],
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
		];
	}

	/**
	 * @dataProvider namedProvider()
	 */
	public function testNamed($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->named());
	}

	public function groupsProvider()
	{
		return [
			[
				[
					['555-1212'],
					['1-800-555-1212']
				],
				$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject = "Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['555-1212'],
					['1-800-555-1212']
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
			[
				[
					['Call', '555-1212'],
					['or', '1-800-555-1212']
				],
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				$subject,
			],
		];
	}

	/**
	 * @dataProvider groupsProvider()
	 */
	public function testGroups($expected, $pattern, $subject)
	{
		$matches = $this->match($pattern, $subject);
		$this->assertEquals($expected, $matches->groups());
	}

	public function getProvider()
	{
		return [

			[
				new Matches(
					$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['555-1212', '555-1212'], true),
				$pattern,
				$subject,
				0
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject, [' or 1-800-555-1212', '1-800-555-1212'], true),
				$pattern,
				$subject,
				1
			],
			[
				new Matches(
					$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['Call 555-1212', 'Call', '555-1212'], true),
				$pattern,
				$subject,
				0
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212 and 254-3434",
					[' and 254-3434', '254-3434'], true),
				$pattern,
				$subject,
				new StringObject('2')
			],
			[
				null,
				$pattern,
				$subject,
				7
			],
			[
				'Nothing',
				$pattern,
				$subject,
				7,
				'Nothing'
			],
		];
	}

	/**
	 * @dataProvider getProvider()
	 */
	public function testGet($expected, $pattern, $subject, $key = null, $default =null, $flags = 0, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches->get($key, $default));
	}

	public function getThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	{
		return [
			[
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				10
			]
		];
	}

	/**
	 * @dataProvider getThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	 * @expectedException \Tea\Regex\Exception\GroupDoesNotExist
	 */
	public function testGetThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject);
		$matches->get($key, null, true);
	}


	public function getThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone'
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				new \stdClass,
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider getThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function testGetThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject);
		$matches->get($key);
	}


	public function groupProvider()
	{
		return [
			[
				new Matches(
					$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['555-1212', '555-1212'], true),
				$pattern,
				$subject,
				[0]
			],
			[
				[
					new Matches(
						$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject, ['Call 555-1212', '555-1212'], true),
					new Matches(
						$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject, [' or 1-800-555-1212', '1-800-555-1212'], true),
				],
				$pattern,
				$subject,
				[0, 1]
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212 and 254-3434",
					[' and 254-3434', '254-3434'], true),
				$pattern,
				$subject,
				[new StringObject('2')]
			],
			[
				[
					1 => new Matches(
						$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212 and 254-3434",
						[' or 1-800-555-1212', '1-800-555-1212'], true),
					2 => new Matches($pattern, $subject, [' and 254-3434', '254-3434'], true),
				],
				$pattern,
				$subject,
				[1, new StringObject('2')]
			],
		];
	}

	/**
	 * @dataProvider groupProvider()
	 */
	public function testGroup($expected, $pattern, $subject, $groups, $flags = 0, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);

		$actual = call_user_func_array([$matches, 'group'], $groups);
		$this->assertEquals($expected, $actual);
	}


	public function groupThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'foo',
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				3,
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[0, 1, 2, 3],
			],
		];
	}

	/**
	 * @dataProvider groupThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	 * @expectedException \Tea\Regex\Exception\GroupDoesNotExist
	 */
	public function _testGroupThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $groups)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$actual = call_user_func_array([$matches, 'group'], (array) $groups);
	}


	public function groupThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[new \stdClass],
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[[0, 1]],
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[0, [0, 1], 1],
			],
		];
	}

	/**
	 * @dataProvider groupThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function _testGroupThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $groups)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$actual = call_user_func_array([$matches, 'group'], (array) $groups);
	}

	public function hasProvider()
	{
		return [
			[
				true,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				0,
				false,
			],
			[
				true,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				1,
				false,
			],
			[
				false,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				2,
				false,
			],
			[
				true,
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone',
				true,
			],
			[
				false,
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'foo',
				true,
			],
		];
	}

	/**
	 * @dataProvider hasProvider()
	 */
	public function _testHas($expected, $pattern, $subject, $key, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches->has($key);
		$this->assertInternalType('boolean', $actual);
		$this->assertEquals($expected, $actual);
	}


	public function hasThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				new \stdClass,
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider hasThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function _testHasThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches->has($key);
	}


	public function countProvider()
	{
		return [
			[
				1,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				2,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				2,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				2,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				PREG_OFFSET_CAPTURE
			],
			[
				4,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				4,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
				PREG_OFFSET_CAPTURE
			],
			[
				6,
				"/\s*((?:[a-zA-Z]+)\s?(?:[a-zA-Z]+))?\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212 or fax 1-800-444-7273",
				true,
				PREG_OFFSET_CAPTURE
			],
		];
	}

	/**
	 * @dataProvider countProvider()
	 */
	public function _testCount($expected, $pattern, $subject, $globalMatch = false, $flags = null, $offset=0)
	{
		$matches = $this->match($pattern, $subject, $globalMatch, $flags, $offset);
		$this->assertIsMatches($matches);
		$actual = $matches->count();

		$this->assertInternalType('integer', $actual);
		$this->assertEquals(count($matches), $actual);
		$this->assertEquals($expected, $actual);
	}


	public function offsetGetProvider()
	{
		return [
			[
				'555-1212',
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone',
				false,
			],
			[
				['555-1212', '1-800-555-1212'],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone',
				true,
			],
			[
				'Call 555-1212',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				0,
				false,
			],
			[
				'Call',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				1,
				false,
			],
			[
				'555-1212',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				2,
				false,
			],
		];
	}

	/**
	 * @dataProvider offsetGetProvider()
	 */
	public function _testOffsetGet($expected, $pattern, $subject, $key, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches[$key];
		$this->assertEquals($expected, $actual);
	}


	public function offsetGetThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'foo',
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				3,
			],
		];
	}

	/**
	 * @dataProvider offsetGetThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	 * @expectedException \Tea\Regex\Exception\GroupDoesNotExist
	 */
	public function _testOffsetGetThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches[$key];
	}


	public function offsetGetThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				new \stdClass,
			],
			[
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider offsetGetThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function _testOffsetGetThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches[$key];
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function _testOffsetSet()
	{
		$matches = $this->match('/^(foo)/u', 'foobar');
		$this->assertIsMatches($matches);
		$matches[1] = 'foo';
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function _testOffsetUnset()
	{
		$matches = $this->match('/^(foo)/u', 'foobar');
		$this->assertIsMatches($matches);
		unset($matches[1]);
	}

	public function accessNamedGroupAsPropertyProvider()
	{
		return [
			[
				'555-1212',
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone',
				false,
			],
			[
				['555-1212', '1-800-555-1212'],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'phone',
				true,
			],
			[
				'Call',
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'text',
				false,
			],
		];
	}

	/**
	 * @dataProvider accessNamedGroupAsPropertyProvider()
	 */
	public function _testAccessNamedGroupAsProperty($expected, $pattern, $subject, $name, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches->$name;
		$this->assertEquals($expected, $actual);
	}


	public function accessGroupAsPropertyThrowsInvalidGroupIndexProvider()
	{
		return [
			[
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				1,
				false,
			],
		];
	}

	/**
	 * @dataProvider accessGroupAsPropertyThrowsInvalidGroupIndexProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function  _testAccessGroupAsPropertyThrowsInvalidGroupIndex($pattern, $subject, $name, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches->$name;
	}

	public function accessGroupAsPropertyThrowsNamedGroupDoesntExistProvider()
	{
		return [
			[
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				'text',
				false,
			],
		];
	}

	/**
	 * @dataProvider accessGroupAsPropertyThrowsNamedGroupDoesntExistProvider()
	 * @expectedException \Tea\Regex\Exception\NamedGroupDoesntExist
	 */
	public function _testAccessGroupAsPropertyThrowsNamedGroupDoesntExist($pattern, $subject, $name, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches->$name;
	}

	public function iterationProvider()
	{
		return [
			[
				[
					'555-1212',
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['555-1212', '1-800-555-1212'],
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					['555-1212', '1-800-555-1212'],
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'Call',
					'555-1212',
				],
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['Call', 'or'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
		];
	}


	/**
	 * @dataProvider iterationProvider()
	 */
	public function _testIteration($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = [];
		foreach ($matches as $key => $value) {
			$this->assertInternalType('integer', $key);
			$actual[] = $value;
		}
		$this->assertEquals($expected, $actual);
	}
}