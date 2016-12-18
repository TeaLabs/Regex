<?php
namespace Tea\Regex\Tests\Result;

use Tea\Regex\Result\Replacement;

class ReplacementTest extends TestCase
{

	protected function replace($pattern, $replacement, $subject, $limit = -1)
	{
		$replaced = preg_replace($pattern, $replacement, $subject, $limit, $count);
		return new Replacement($pattern, $subject, $replacement, $replaced, $count, $limit);
	}

	/**
	 * Asserts that a variable is of a Matches instance.
	 *
	 * @param mixed $object
	 */
	public function assertInstanceOfReplacement($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Replacement', $object);
	}

	public function testCreate()
	{
		$pattern = '/^(f)/u';
		$replacement = 'b';
		$subject = 'foo';
		$limit = 2;
		$replaced = $this->replace($pattern, $replacement, $subject, $limit);

		$this->assertInstanceOfReplacement($replaced);
		$this->assertEquals($pattern, $replaced->pattern());
		$this->assertEquals($subject, $replaced->subject());
		$this->assertEquals($replacement, $replaced->replacement());
		$this->assertEquals($limit, $replaced->limit());
	}


	public function resultProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],

			[
				[ 'home' => "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
			],


		];
	}

	/**
	 * @dataProvider resultProvider()
	 */
	public function testResult($expected, $pattern, $replacement, $subject, $limit = -1)
	{
		$result = $this->replace($pattern, $replacement, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->result());
	}


	public function countProvider()
	{
		return [
			[
				4,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],

			[
				4,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],
			[
				2,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2
			],
			[
				4,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				2
			],
			[
				20,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				]
			],
			[
				15,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				3
			],
			[
				0,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"722555121 701888020 733446643 +254700072245 +254711345543",
			],
		];
	}

	/**
	 * @dataProvider countProvider()
	 */
	public function testCount($expected, $pattern, $replacement, $subject, $limit = -1)
	{
		$result = $this->replace($pattern, $replacement, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$actual = $result->count();

		$this->assertEquals($expected, $actual);
		$this->assertEquals($actual, count($result));
	}

	public function anyProvider()
	{
		return [
			[
				true,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],

			[
				true,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],
			[
				false,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"722555121 701888020 733446643 +254700072245 +254711345543",
			],
			[
				false,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"722555121 701888020 733446643 +254700072245 +254711345543",
					"722555121 701888020 733446643 +254700072245 +254711345543",
					"722555121 701888020 733446643 +254700072245 +254711345543",
				],
			],
		];
	}

	/**
	 * @dataProvider anyProvider()
	 */
	public function testAny($expected, $pattern, $replacement, $subject, $limit = -1)
	{
		$result = $this->replace($pattern, $replacement, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$actual = $result->any();
		$this->assertInternalType('boolean', $actual);
		$this->assertEquals($expected, $actual);
	}

	public function getProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				null,
				null,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				null,
				null,
			],

			[
				"+254722555121",
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'home',
				null,
			],

			[
				"+254733446643",
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				2,
				null,
			],
			[
				null,
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
				null,
			],

			[
				null,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				9,
				null,
			],
			[
				"+254733446643",
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
				"+254733446643",
			],
		];
	}

	/**
	 * @dataProvider getProvider()
	 */
	public function testGet($expected, $pattern, $replacement, $subject, $key = null, $default = null)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$actual = $result->get($key, $default);
		$this->assertEquals($expected, $actual);
	}

	public function getThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				1
			]
		];
	}

	/**
	 * @dataProvider getThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	 * @expectedException \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function testGetThrowsIllegalReplacementTypeAccessOnStringReplacement($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result->get($key);
	}


	public function getThrowsUnknownReplacementKeyOnMissingKeyProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
			],

			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				9,
			],
		];
	}

	/**
	 * @dataProvider getThrowsUnknownReplacementKeyOnMissingKeyProvider()
	 * @expectedException \Tea\Regex\Exception\UnknownReplacementKey
	 */
	public function testGetThrowsUnknownReplacementKeyOnMissingKey($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result->get($key, null, true);
	}

	public function getThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				new \stdClass,
			],
			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider getThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidReplacementKey
	 */
	public function testGetThrowsInvalidReplacementKeyWhenKeyIsInvalid($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result->get($key);
	}

	public function hasProvider()
	{
		return [
			[
				true,
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'home',
			],

			[
				true,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				2,
			],
			[
				false,
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
			],

			[
				false,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				9,
			],
		];
	}

	/**
	 * @dataProvider hasProvider()
	 */
	public function testHas($expected, $pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$actual = $result->has($key);
		$this->assertInternalType('boolean', $actual);
		$this->assertEquals($expected, $actual);
	}


	public function hasThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				1
			]
		];
	}

	/**
	 * @dataProvider hasThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	 * @expectedException \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function testHasThrowsIllegalReplacementTypeAccessOnStringReplacement($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result->has($key);
	}


	public function hasThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				new \stdClass,
			],
			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider hasThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidReplacementKey
	 */
	public function testHasThrowsInvalidReplacementKeyWhenKeyIsInvalid($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result->has($key);
	}


	public function offsetGetProvider()
	{
		return [
			[
				"+254722555121",
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'home',
			],

			[
				"+254733446643",
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				2,
			],
		];
	}

	/**
	 * @dataProvider offsetGetProvider()
	 */
	public function testOffsetGet($expected, $pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$actual = $result[$key];
		$this->assertEquals($expected, $actual);
	}


	public function offsetGetThrowsUnknownReplacementKeyOnMissingKeyProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
			],

			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				9,
			],
		];
	}

	/**
	 * @dataProvider offsetGetThrowsUnknownReplacementKeyOnMissingKeyProvider()
	 * @expectedException \Tea\Regex\Exception\UnknownReplacementKey
	 */
	public function testOffsetGetThrowsUnknownReplacementKeyOnMissingKey($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result[$key];
	}


	public function offsetGetThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				1
			]
		];
	}

	/**
	 * @dataProvider offsetGetThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	 * @expectedException \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function testOffsetGetThrowsIllegalReplacementTypeAccessOnStringReplacement($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result[$key];
	}


	public function offsetGetThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				new \stdClass,
			],
			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider offsetGetThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidReplacementKey
	 */
	public function testOffsetGetThrowsInvalidReplacementKeyWhenKeyIsInvalid($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$result[$key];
	}


	public function offsetExistsProvider()
	{
		return [
			[
				true,
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'home',
			],

			[
				true,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				2,
			],
			[
				false,
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				'mobile',
			],

			[
				false,
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				9,
			],
		];
	}

	/**
	 * @dataProvider offsetExistsProvider()
	 */
	public function testOffsetExists($expected, $pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$actual = isset($result[$key]);
		$this->assertInternalType('boolean', $actual);
		$this->assertEquals($expected, $actual);
	}


	public function offsetExistsThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				1
			]
		];
	}

	/**
	 * @dataProvider offsetExistsThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	 * @expectedException \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function testOffsetExistsThrowsIllegalReplacementTypeAccessOnStringReplacement($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		isset($result[$key]);
	}


	public function offsetExistsThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	{
		return [
			[
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"],
				new \stdClass,
			],
			[
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				[0, 1],
			],
		];
	}

	/**
	 * @dataProvider offsetExistsThrowsInvalidReplacementKeyWhenKeyIsInvalidProvider()
	 * @expectedException \Tea\Regex\Exception\InvalidReplacementKey
	 */
	public function testOffsetExistsThrowsInvalidReplacementKeyWhenKeyIsInvalid($pattern, $replacement, $subject, $key)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		isset($result[$key]);
	}


	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testOffsetSet()
	{
		$result = $this->replace('/^(foo)/u', 'FOO', 'foobar');
		$this->assertInstanceOfReplacement($result);
		$result[1] = 'foo';
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testOffsetUnset()
	{
		$result = $this->replace('/^(foo)/u', 'FOO', 'foobar');
		$this->assertInstanceOfReplacement($result);
		unset($result[1]);
	}

	public function iterationProvider()
	{
		return [
			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543")
			],

			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"]
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				]
			],
		];
	}


	/**
	 * @dataProvider iterationProvider()
	 */
	public function testIteration($expected, $pattern, $replacement, $subject)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$actual = [];
		foreach ($result as $key => $value) {
			$actual[$key] = $value;
		}
		$this->assertEquals($expected, $actual);
	}


	public function iterationThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			]
		];
	}

	/**
	 * @dataProvider iterationThrowsIllegalReplacementTypeAccessOnStringReplacementProvider()
	 * @expectedException \Tea\Regex\Exception\IllegalReplacementTypeAccess
	 */
	public function testIterationThrowsIllegalReplacementTypeAccessOnStringReplacement($pattern, $replacement, $subject)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		foreach ($result as $key => $value) {
			continue;
		}
	}

	public function toStringProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"+254722555121",
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],
		];
	}

	/**
	 * @dataProvider toStringProvider()
	 */
	public function testToString($expected, $pattern, $replacement, $subject)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result);
		$this->assertEquals($expected, (string) $result);
	}


	public function toArrayProvider()
	{
		return [
			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],
			[
				["+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"]
			],
		];
	}

	/**
	 * @dataProvider toArrayProvider()
	 */
	public function testToArray($expected, $pattern, $replacement, $subject)
	{
		$result = $this->replace($pattern, $replacement, $subject);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->toArray());
	}


	public function replacedProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				null,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
			],
			[
				null,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],

			[
				['home' =>  "+254722555121", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "+254701888020", 'cell' => "0733446643"],
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
			],
			[
				[
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				2
			],
		];
	}

	/**
	 * @dataProvider replacedProvider()
	 */
	public function testReplaced($expected, $pattern, $replacement, $subject, $limit = -1)
	{
		$result = $this->replace($pattern, $replacement, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->replaced());
	}


}