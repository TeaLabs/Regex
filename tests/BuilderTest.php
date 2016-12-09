<?php
namespace Tea\Tests\Regex;

use Tea\Regex\Regex;
use Tea\Regex\Builder;

class BuilderTest extends TestCase
{

	public function rawProvider()
	{
		return [
			[ [], '/^abc.+xyz$/ui' ],
			[ [], '/abc.+xyz$ui' ],
			[ [], '^abc.+xyz$' ],
			[ [], 'xxẦ' ],
			[ [], '' ],
			[ [], '/^(abc(?:xx))(\s).+(\d)(?:skp)(\1)$/u' ],
		];
	}

	/**
	 * @dataProvider rawProvider()
	 */
	public function testRaw($expected, $pattern, $flags = 0, $builder = null)
	{
		$builder = $builder ?: new Builder;
		$ex = '0l';
		$encoding = $ex ? 'ASCII' : 'NOT-ASCII';
		// echo "\n*****\n Encoding : {$encoding}";

		// $matches = $builder->raw($pattern, $flags);

		// foreach ($matches as $key => &$match) {
		// 	$match = json_encode($match);
		// }
		// $ms = print_r((string) $matches, true);
		// $ms = str_replace(["\n"], ["\n  "], $ms);
		// echo "\n*****\n Pattern {$pattern} : {$ms}";
	}
}