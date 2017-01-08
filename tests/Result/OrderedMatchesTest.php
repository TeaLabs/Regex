<?php
namespace Tea\Regex\Tests\Result;

use Tea\Regex\Flags;
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
						['555-1212', '555-1212'], true, PREG_SET_ORDER, false),
					new Matches($pattern, $subject, ['1-800-555-1212', '1-800-555-1212'], true, PREG_SET_ORDER, false),
				],
				$pattern,
				$subject,
			],
			[
				[
					new Matches(
						$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212",
						['Call 555-1212', '555-1212'], true, PREG_SET_ORDER, false),
					new Matches($pattern, $subject, [' or 1-800-555-1212', '1-800-555-1212'], true, PREG_SET_ORDER, false),
				],
				$pattern,
				$subject,
			],
			[
				[
					new Matches(
						$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
						$subject = "Call 555-1212 or 1-800-555-1212",
						['Call 555-1212', 'Call', '555-1212'], true, PREG_SET_ORDER, false),
					new Matches($pattern, $subject, [' or 1-800-555-1212', 'or', '1-800-555-1212'], true, PREG_SET_ORDER, false),
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

	public function testDefault()
	{
		$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u";
		$subject = "1-800-343-4534 Call 555-1212 or 1-800-555-1212 0-800-343-4534";
		$default = 'default';

		$matches = $this->match($pattern, $subject);
		$matches->default($default);
		$this->assertEquals( [
			['1-800-343-4534', $default, '1-800-343-4534'],
			[' Call 555-1212', 'Call', '555-1212'],
			[' or 1-800-555-1212', 'or', '1-800-555-1212'],
			[' 0-800-343-4534', $default, '0-800-343-4534'],
		], $matches->toArray());


		$matches = $this->match($pattern, $subject, Flags::OFFSET_CAPTURE);
		$matches->default($default);

		$this->assertEquals( [
			[["1-800-343-4534",0],[$default, 0],["1-800-343-4534",0]],
			[[" Call 555-1212",14],["Call",15],["555-1212",20]],
			[[" or 1-800-555-1212",28],["or",29],["1-800-555-1212",32]],
			[[" 0-800-343-4534",46],[$default, 47],["0-800-343-4534",47]]
		], $matches->toArray());

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
					['555-1212', '555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				0
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject, [' or 1-800-555-1212', '1-800-555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				1
			],
			[
				new Matches(
					$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['Call 555-1212', 'Call', '555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				0
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212 and 254-3434",
					[' and 254-3434', '254-3434'], true, PREG_SET_ORDER, false),
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

	public function hasProvider()
	{
		return [
			[
				true,
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				1,
			],
			[
				false,
				"/(?P<phone>\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				2,
			],
		];
	}

	/**
	 * @dataProvider hasProvider()
	 */
	public function testHas($expected, $pattern, $subject, $key, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
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
		$matches = $this->match($pattern, $subject);
		$matches->has($key);
	}


	public function countProvider()
	{
		return [
			[
				2,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
				false,
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
		];
	}

	/**
	 * @dataProvider countProvider()
	 */
	public function testCount($expected, $pattern, $subject, $all = false, $flags = null, $offset=0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$actual = $matches->count($all);

		$this->assertInternalType('integer', $actual);
		$this->assertEquals($expected, $actual);

		if(!$all)
			$this->assertEquals(count($matches), $actual);

	}


	public function offsetGetProvider()
	{
		return [
			[
				new Matches(
					$pattern = "/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['555-1212', '555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				0
			],
			[
				new Matches(
					$pattern = "/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject, [' or 1-800-555-1212', '1-800-555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				1
			],
			[
				new Matches(
					$pattern = "/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
					$subject = "Call 555-1212 or 1-800-555-1212",
					['Call 555-1212', 'Call', '555-1212'], true, PREG_SET_ORDER, false),
				$pattern,
				$subject,
				0
			],
		];
	}

	/**
	 * @dataProvider offsetGetProvider()
	 */
	public function testOffsetGet($expected, $pattern, $subject, $key, $flags = null, $offset = 0)
	{
		$matches = $this->match($pattern, $subject, $flags, $offset);
		$this->assertEquals($expected, $matches[$key]);
	}


	public function offsetGetThrowsGroupDoesNotExistWhenIndexIsMissingProvider()
	{
		return [
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
		$matches = $this->match($pattern, $subject);
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
		$matches = $this->match($pattern, $subject);
		$matches[$key];
	}
}