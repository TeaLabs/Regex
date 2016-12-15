<?php
namespace Tea\Regex\Tests\Result;

use Tea\Regex\Result\Matches;

class MatchesTest extends TestCase
{

	protected function match($pattern, $subject, $globalMatch = false, $flags = null, $offset = 0)
	{
		if(is_null($flags))
			$flags = $globalMatch ? PREG_PATTERN_ORDER : 0;

		if($globalMatch)
			$result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
		else
			$result = preg_match($pattern, $subject, $matches, $flags, $offset);

		return new Matches($pattern, $subject, $matches, $result, $globalMatch);
	}

	/**
	 * Asserts that a variable is of a Matches instance.
	 *
	 * @param mixed $object
	 */
	public function assertIsMatches($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Matches', $object);
	}

	public function testCreate()
	{
		$pattern = '/^f/u';
		$subject = 'foo';
		$matches = $this->match($pattern, $subject);

		$this->assertIsMatches($matches);
		$this->assertEquals($pattern, $matches->pattern());
		$this->assertEquals($subject, $matches->subject());
	}


	public function allProvider()
	{
		return [
			[
				[
					'555-1212',
					'555-1212',
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['555-1212', '1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'Call 555-1212',
					'555-1212',
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'Call 555-1212',
					'Call',
					'555-1212',
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['Call', 'or'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
		];
	}

	/**
	 * @dataProvider allProvider()
	 */
	public function testAll($expected, $pattern, $subject, $globalMatch = false, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $globalMatch, $flags, $offset);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->all());
	}

	/**
	 * @dataProvider allProvider()
	 */
	public function testResult($expected, $pattern, $subject, $globalMatch = false, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $globalMatch, $flags, $offset);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->result());
	}

	public function anyProvider()
	{
		return [
			[
				true,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				false,
				"/\s*(?:[a-zA-Z]+)\s*(?:\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})\s*(\.{1})\s*/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			]
		];
	}

	/**
	 * @dataProvider anyProvider()
	 */
	public function testAny($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = $matches->any();
		$this->assertInternalType('boolean', $actual);
		$this->assertEquals($expected, $actual);
	}



	public function indexedGroupsProvider()
	{
		return [
			[
				[
					'555-1212',
					'555-1212',
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['555-1212', '1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'Call 555-1212',
					'555-1212',
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'Call 555-1212',
					'Call',
					'555-1212',
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['Call', 'or'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
		];
	}

	/**
	 * @dataProvider indexedGroupsProvider()
	 */
	public function testIndexedGroups($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->indexedGroups());
	}

	public function namedGroupsProvider()
	{
		return [
			[
				[],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					'phone' => '555-1212',
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					'phone' => ['555-1212', '1-800-555-1212']
				],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'phone' => '555-1212',
				],
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					'phone' => ['555-1212', '1-800-555-1212']
				],
				"/\s*(?:[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'text' => 'Call',
					'phone' => '555-1212',
				],
				"/\s*(?P<text>[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
			],
			[
				[
					'phone' => ['555-1212', '1-800-555-1212'],
					'end' => ['', '']
				],
				"/\s*(?:[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})(?P<end>\.{0,1})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
			[
				[
					'phone' => ['555-1212', '1-800-555-1212'],
					'end' => ['', '.']
				],
				"/\s*(?:[a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})(?P<end>\.{0,1})/u",
				"Call 555-1212 or 1-800-555-1212.",
				true,
			],
		];
	}

	/**
	 * @dataProvider namedGroupsProvider()
	 */
	public function testNamedGroups($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->namedGroups());
	}

	/**
	 * @dataProvider namedGroupsProvider()
	 */
	public function testNamed($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->named());
	}

	public function groupsProvider()
	{
		$indexed = array_map(function($args){
			array_shift($args[0]);
			return $args;
		}, $this->indexedGroupsProvider());

		$named = array_map(function($args){
			$args[] = true;
			return $args;
		}, $this->namedGroupsProvider());

		return array_merge($indexed, $named);
	}

	/**
	 * @dataProvider groupsProvider()
	 */
	public function testGroups($expected, $pattern, $subject, $globalMatch = false, $namedGroups = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->groups($namedGroups));
	}

	public function getProvider()
	{
		return [
			[
				[
					'555-1212',
					'555-1212',
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				null,
				null
			],
			[
				'555-1212',
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				'phone'
			],
			[
				['555-1212', '1-800-555-1212'],
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
				'phone',
			],
			[
				'Call 555-1212',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				0
			],
			[
				'Call',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				1
			],
			[
				null,
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				'foo'
			],
			[
				'bar',
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
				'foo',
				'bar'
			]
		];
	}

	/**
	 * @dataProvider getProvider()
	 */
	public function testGet($expected, $pattern, $subject, $globalMatch = false, $key = null, $default =null)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$this->assertEquals($expected, $matches->get($key, $default));
	}

	public function getThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
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
	 * @dataProvider getThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	 * @expectedException \Tea\Regex\Exception\GroupDoesNotExist
	 */
	public function testGetThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches->get($key, null, true);
	}


	public function getThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
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
	 * @dataProvider getThrowsInvalidGroupIndexWhenIndexIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidGroupIndex
	 */
	public function testGetThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches->get($key, null, true);
	}


	public function groupProvider()
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
				[ 1 => 'Call', 2 => '555-1212'],
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[1, 2],
				false,
			],
			[
				[ 2 => '555-1212', 'phone' => '555-1212', ],
				"/\s*([a-zA-Z]*)\s*(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				[2, 'phone'],
				false,
			],
		];
	}

	/**
	 * @dataProvider groupProvider()
	 */
	public function testGroup($expected, $pattern, $subject, $groups, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
		$this->assertIsMatches($matches);
		$actual = call_user_func_array([$matches, 'group'], (array) $groups);
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
	public function testGroupThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $groups)
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
	public function testGroupThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $groups)
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
	public function testHas($expected, $pattern, $subject, $key, $globalMatch = false)
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
	public function testHasThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
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
				4,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				true,
			],
		];
	}

	/**
	 * @dataProvider countProvider()
	 */
	public function testCount($expected, $pattern, $subject, $globalMatch = false)
	{
		$matches = $this->match($pattern, $subject, $globalMatch);
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
	public function testOffsetGet($expected, $pattern, $subject, $key, $globalMatch = false)
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
	public function testOffsetGetThrowsGroupDoesNotExistWhenIndexIsMissing($pattern, $subject, $key)
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
	public function testOffsetGetThrowsInvalidGroupIndexWhenIndexIsInvalid($pattern, $subject, $key)
	{
		$matches = $this->match($pattern, $subject, false);
		$this->assertIsMatches($matches);
		$matches[$key];
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testOffsetSet()
	{
		$matches = $this->match('/^(foo)/u', 'foobar');
		$this->assertIsMatches($matches);
		$matches[1] = 'foo';
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testOffsetUnset()
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
	public function testAccessNamedGroupAsProperty($expected, $pattern, $subject, $name, $globalMatch = false)
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
	public function testAccessGroupAsPropertyThrowsInvalidGroupIndex($pattern, $subject, $name, $globalMatch = false)
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
	public function testAccessGroupAsPropertyThrowsNamedGroupDoesntExist($pattern, $subject, $name, $globalMatch = false)
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
	public function testIteration($expected, $pattern, $subject, $globalMatch = false)
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