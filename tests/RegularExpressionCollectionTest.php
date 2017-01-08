<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Config;
use Tea\Regex\Builder;
use Tea\Regex\RegularExpression;
use Tea\Regex\RegularExpressionCollection;
use Tea\Regex\Tests\Mocks\StringObject;

class RegularExpressionCollectionTest extends TestCase
{
	protected function create($patterns = [], $modifiers = null, $delimiter = null)
	{
		return RegularExpressionCollection::create($patterns, $modifiers, $delimiter);
	}

	protected function from($body, $modifiers =null, $delimiter = null)
	{
		return RegularExpression::from($body, $modifiers, $delimiter);
	}

	/**
	 * Asserts that a variable is of a Matches instance.
	 *
	 * @param mixed $object
	 */
	public function assertInstanceOfMatches($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Matches', $object);
	}


	/**
	 * Asserts that a variable is of a Replacement instance.
	 *
	 * @param mixed $object
	 */
	public function assertInstanceOfReplacement($object)
	{
		$this->assertInstanceOf('Tea\Regex\Result\Replacement', $object);
	}


	/**
	 * Asserts that a variable is of a RegularExpression instance.
	 *
	 * @param mixed $object
	 */
	public function assertIsRegularExpression($object)
	{
		$this->assertInstanceOf('Tea\Regex\RegularExpression', $object);
	}

	public function testCreate()
	{
		$p1 = new RegularExpression('(?:^|(\s+))0(7\d{2})', 'x', '#');
		$p2 = '(?:^|(\s+))0(7\d{2})';
		$p3 = ['(?:^|(\s+))0(7\d{2})', 'i', '|'];
		$re = $this->create([$p1, $p2, $p3], 's', '~');

		$this->assertIsRegularExpression($re[0]);
		$this->assertEquals('#(?:^|(\s+))0(7\d{2})#x', $re[0]);

		$this->assertIsRegularExpression($re[1]);
		$this->assertEquals('~(?:^|(\s+))0(7\d{2})~s', $re[1]);

		$this->assertIsRegularExpression($re[2]);
		$this->assertEquals('|(?:^|(\s+))0(7\d{2})|i', $re[2]);

		$re = $this->create([], 's', '~');
		$re[] = $p1;
		$re[] = $p2;
		$re[] = $p3;

		$this->assertIsRegularExpression($re[0]);
		$this->assertEquals('#(?:^|(\s+))0(7\d{2})#x', $re[0]);

		$this->assertIsRegularExpression($re[1]);
		$this->assertEquals('~(?:^|(\s+))0(7\d{2})~s', $re[1]);

		$this->assertIsRegularExpression($re[2]);
		$this->assertEquals('|(?:^|(\s+))0(7\d{2})|i', $re[2]);

	}

	public function replaceProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/(?<=^|\s)0(7\d{2})/ux"],
				'+254$1',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				["/^0(7\d{2})/u"],
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],

			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				["/^0(7\d{2})/u"],
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
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				-1,
				20
			],
			[
				[
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				],
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				2,
				10
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				["/ (?: ^| (?P<space> \s+ ) ) (?P<netId> 07\d{2} )/ux"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2
			],
		];
	}

	/**
	 * @dataProvider replaceProvider()
	 */
	public function testReplace($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);

		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			$result = $regex->replace($replace, $subject, $limit);
		}

		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->result());
		if(!is_null($count)){
			$this->assertEquals($count, $result->count());
		}
	}


	public function replaceThrowsReplacementErrorProvider()
	{
		return [
			[
				[RegularExpression::create("/(?:^|(\s+))0(7\d{2})", 'qwerty')],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:^|(\s+))0(7\d{2}/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				"",
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replaceThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplaceThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);
		$regex->replace($replace, $subject, $limit);
	}

	public function replaceCallbackProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				["/ (?: ^| (?P<space> \s+ ) ) (?P<netId> 07\d{2} )/ux"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/ (?: ^| (?P<space> \s+ ) ) (?P<prefix> (?:\+{0,1}254 | 07\d{2}) )/ux"],
				function($matches){
					if($matches->prefix === '+254')
						return $matches[1] . $matches[2];
					elseif (strpos($matches->prefix, '254') === 0)
						return $matches[1] . '+'. $matches[2];
					elseif (strpos($matches->prefix, '07') === 0)
						return $matches[1].'+254'. substr($matches[2], 1);
					else
						return $matches[1] . $matches[2];
				},
				"0722555121 0701888020 254733446643 254700072245 +254711345543",
				-1,
				5
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				["/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				-1,
				4
			],

			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				["/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
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
				["/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u"],
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				-1,
				20
			],
		];
	}

	/**
	 * @dataProvider replaceCallbackProvider()
	 */
	public function testReplaceCallback($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$callback = function($matches) use ($replace){
			$this->assertInstanceOfMatches($matches);
			return $replace($matches);
		};

		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);

		$result = $regex->replaceCallback($callback, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->result());
		if(!is_null($count)){
			$this->assertEquals($count, $result->count());
		}
	}

	public function replaceCallbackThrowsReplacementErrorProvider()
	{
		return [
			[
				[RegularExpression::create("(?:^|(\s+))0(7\d{2})", 'qwerty')],
				function($matches){
					return $matches->space.'+254'.$matches->provider;
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				["/(?:^|(?P<space>\s+))0(?P<provider>7\d{2}/u"],
				function($matches){
					return $matches->space.'+254'.$matches->provider;
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				["/(?:\D+|<\d+>)*[!?]/"],
				function($matches){
					return $matches[0];
				},
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replaceCallbackThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplaceCallbackThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);
		$regex->replaceCallback($replace, $subject, $limit);
	}


	public function replacedProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				null,
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				-1,
			],
			[
				null,
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
				-1,
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
				["/^0(7\d{2})/u"],
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				-1,
				4
			],

			[
				['home' =>  "+254722555121", 'cell' => "+254733446643"],
				["/^0(7\d{2})/u"],
				'+254$1',
				['home' =>  "0722555121", 'office' => "+254701888020", 'cell' => "0733446643"],
				-1,
				2
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				-1,
				16
			],
			[
				[
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				],
				["/(?:^|(\s+))0(7\d{2})/u"],
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				2,
				8
			],
		];
	}

	/**
	 * @dataProvider replacedProvider()
	 */
	public function testReplaced($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);
		$result = $regex->replaced($replace, $subject, $limit);

		if(!is_null($expected)){
			$this->assertInstanceOfReplacement($result);
			$this->assertEquals($expected, $result->result());
			if(!is_null($count)){
				$this->assertEquals($count, $result->count());
			}
		}
		else{
			$this->assertNull($result);
		}

	}


	public function replacedThrowsReplacementErrorProvider()
	{
		return [
			[
				[RegularExpression::create("(?:^|(\s+))0(7\d{2})", 'qwerty')],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				["/(?:^|(\s+))0(7\d{2}/u"],
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				["/(?:\D+|<\d+>)*[!?]/"],
				'',
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replacedThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplacedThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		$regex = $this->from($pattern);
		$this->assertIsRegularExpression($regex);
		$regex->replaced($replace, $subject, $limit);
	}

}
